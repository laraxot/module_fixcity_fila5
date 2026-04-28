import { LitElement, html } from 'lit';
import { guard } from 'lit/directives/guard.js';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { mapPickerStyles, controlIcons } from './map-picker-styles.js';
import { createMapPickerLeafletIcon } from './map-picker-marker-config.js';

/**
 * CoordinatePickerField
 * ZEN: Absolute technical precision and wizard stability.
 * Implementation: Guarded Light DOM with ResizeObserver.
 */
export class CoordinatePickerField extends LitElement {
    static properties = {
        state: { type: Object }, // Accepts {latitude, longitude} object
        zoom: { type: Number },
        height: { type: String },
        isLocating: { type: Boolean, state: true },
        isFullscreen: { type: Boolean, state: true },
        geolocateWhenEmpty: { type: Boolean, attribute: 'geolocate-when-empty' },
        labels: { type: Object },
        provider: { type: String },
        showSearch: { type: Boolean, attribute: 'show-search' },
        _isProgrammaticUpdate: { type: Boolean, state: true },
    };

    get _lat() { return this.state?.latitude ?? null; }
    get _lng() { return this.state?.longitude ?? null; }

    createRenderRoot() {
        return this;
    }

    constructor() {
        super();
        this.state = null;
        this.zoom = 13;
        this.height = '400px';
        this.isLocating = false;
        this.isFullscreen = false;
        this.geolocateWhenEmpty = false;
        this.labels = {};
        this.provider = 'osm';
        this.showSearch = false;
        this._isProgrammaticUpdate = false;

    // Debounced helper to prevent excessive map updates
    _debounceMapUpdate(fn, delay = 300) {
        clearTimeout(this._debounceTimeout);
        this._debounceTimeout = setTimeout(() => {
            fn();
        }, delay);
    }

    _handleMapInteraction(lat, lng, source = 'manual') {
        this._isProgrammaticUpdate = true;
        const latitude = Number.parseFloat(Number.parseFloat(lat).toFixed(6));
        const longitude = Number.parseFloat(Number.parseFloat(lng).toFixed(6));

        if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
            this._isProgrammaticUpdate = false;
            return;
        }

        this.state = { ...(this.state || {}), latitude, longitude };
        this._updateMarker(latitude, longitude);

        this.dispatchEvent(new CustomEvent('coords-changed', {
            detail: { latitude, longitude, source },
            bubbles: true,
            composed: true,
        }));

        window.setTimeout(() => {
            this._isProgrammaticUpdate = false;
        }, 100);
    }

    // Geolocation fallback when coordinates are null - prevents double requests
    _requestGeolocation() {
        if (!navigator.geolocation || this._geolocated) return;
        this._geolocated = true;
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                this._handleMapInteraction(lat, lng, 'geolocation');
            },
            () => {
                // Failed geolocation - reset flag so we can retry if coordinates become null later
                this._geolocated = false;
            },
            { enableHighAccuracy: true, timeout: 5000 }
        );
    }

    // Centralized map size refresh with staggered intervals to prevent flicker
    _refreshMapSize() {
        // Visibility guard - skip if container is hidden
        if (this.offsetParent === null) return;

        const pane = this.renderRoot.querySelector('.map-picker-leaflet-pane');
        if (!pane) return;

        const rect = pane.getBoundingClientRect();
        if (!rect || rect.width === 0 || rect.height === 0) return;

        // Only refresh if size has actually changed significantly
        const last = this._lastMeasuredSize;
        if (last && Math.abs(last.width - rect.width) < 0.5 && Math.abs(last.height - rect.height) < 0.5) {
            return;
        }

        // Staggered timeout intervals for Filament 5 wizard compatibility
        [0, 50, 150, 300, 500, 800, 1200].forEach((delay) => {
            setTimeout(() => {
                if (this.offsetParent === null || !this._map) return;
                this._map.invalidateSize({ animate: false, pan: false });
                this._forceTileRedraw();
            }, delay);
        });

        // Sync marker and center after refresh
        if (this._lat != null && this._lng != null) {
            this._updateMarker(this._lat, this._lng);
        } else if (this.geolocateWhenEmpty && !this._geolocated) {
            this._requestGeolocation();
        }

        this._lastMeasuredSize = { width: rect.width, height: rect.height };
    }

    // Observer setup - single coordinated observer to prevent multiple refreshes
    _setupObservers() {
        // ResizeObserver - debounced
        this._resizeObserver = new ResizeObserver(() => this._refreshMapSize());
        this._resizeObserver.observe(this);

        // MutationObserver - watches for x-show/toggle in wizard steps (depth 15 for Filament 5 wizard wrappers)
        this._mutationObserver = new MutationObserver(() => {
            if (this.offsetParent !== null) {
                this._refreshMapSize();
            }
        });
        let parent = this.parentElement;
        for (let i = 0; i < 15 && parent; i++) {
            this._mutationObserver.observe(parent, { attributes: true, attributeFilter: ['class', 'style', 'hidden'] });
            parent = parent.parentElement;
        }

        // VisibilityObserver using IntersectionObserver as fallback
        this._visibilityObserver = new IntersectionObserver((entries) => {
            if (entries.some(entry => entry.isIntersecting)) {
                this._refreshMapSize();
            }
        }, { threshold: 0.01 });
        this._visibilityObserver.observe(this);
    }

    render() {
        const l = this.labels || {};
        
        return html`
            <style>
                coordinate-picker-lit { display: block; width: 100%; height: 100%; min-height: 200px; }
                ${mapPickerStyles}
                .map-container { min-height: 200px; }
                .map-container.is-fullscreen { position: fixed !important; inset: 0 !important; width: 100vw !important; height: 100vh !important; border: none !important; border-radius: 0 !important; z-index: 9999 !important; }
                .map-container.is-fullscreen .map-picker-leaflet-pane { height: 100vh !important; }
                .layer-controls-overlay { display: flex !important; flex-direction: column !important; gap: 0.5rem !important; }
            </style>
            <div class="map-container ${this.isFullscreen ? 'is-fullscreen' : ''}" style="--map-height: ${this.height}">
                
                ${guard([], () => html`<div class="map-picker-leaflet-pane" style="height: 100%;"></div>`)}
                
                <div class="layer-controls-overlay">
                    <button class="ctrl-btn" type="button" @click="${this._toggleFullscreen}" title="${this.isFullscreen ? (l.close_fullscreen || 'Chiudi') : (l.fullscreen || 'Fullscreen')}">
                        ${this.isFullscreen ? controlIcons.fullscreenExit : controlIcons.fullscreen}
                    </button>
                    
                    <button class="ctrl-btn" type="button" @click="${this._requestGeolocation}" ?disabled="${this.isLocating}" title="${l.use_location || 'Mia posizione'}">
                        ${this.isLocating 
                            ? html`<svg class="animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" opacity=".25"/><path d="M4 12a8 8 0 018-8" opacity=".75"/></svg>`
                            : controlIcons.crosshair
                        }
                    </button>
                    
                    <button class="ctrl-btn" type="button" @click="${this._switchLayer}" title="${l.switch_layer || 'Cambia Layer'}">
                        ${controlIcons.layer}
                    </button>
                    
                    <button class="ctrl-btn" type="button" @click="${this._zoomIn}" title="${l.zoom_in || 'Zoom In'}">
                        ${controlIcons.zoomIn}
                    </button>
                    <button class="ctrl-btn" type="button" @click="${this._zoomOut}" title="${l.zoom_out || 'Zoom Out'}">
                        ${controlIcons.zoomOut}
                    </button>
                </div>

                <div class="loading-overlay ${this.isLocating ? 'active' : ''}">
                    <div class="spinner"></div>
                </div>
            </div>
        `;
    }

    firstUpdated() {
        this._initMap();
        this._resizeObserver = new ResizeObserver(() => {
            this._refreshMapSize();
        });
        this._resizeObserver.observe(this);
        this._visibilityObserver = new IntersectionObserver((entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                this._refreshMapSize();
            }
        }, { threshold: 0.01 });
        this._visibilityObserver.observe(this);

        // Watch parent elements for Filament wizard step class="hidden" toggle
        this._mutationObserver = new MutationObserver(() => {
            if (this.offsetParent !== null) {
                setTimeout(() => this._refreshMapSize(), 150);
            }
        });
        let parent = this.parentElement;
        for (let i = 0; i < 6 && parent; i++) {
            this._mutationObserver.observe(parent, { attributes: true, attributeFilter: ['class', 'style', 'hidden'] });
            parent = parent.parentElement;
        }

        window.addEventListener('resize', this._boundRefreshMapSize);
        document.addEventListener('livewire:navigated', this._boundRefreshMapSize);
        document.addEventListener('livewire:updated', this._boundRefreshMapSize);
        document.addEventListener('click', this._boundRefreshMapSize, true);
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isFullscreen) this._toggleFullscreen();
        });
    }

    disconnectedCallback() {
        super.disconnectedCallback();
        if (this._map) {
            this._map.remove();
            this._map = null;
        }
        this._resizeObserver?.disconnect();
        this._visibilityObserver?.disconnect();
        this._mutationObserver?.disconnect();
        window.removeEventListener('resize', this._boundRefreshMapSize);
        document.removeEventListener('livewire:navigated', this._boundRefreshMapSize);
        document.removeEventListener('livewire:updated', this._boundRefreshMapSize);
        document.removeEventListener('click', this._boundRefreshMapSize, true);
    }

    updated(changed) {
        if (changed.has('state') && !this._isProgrammaticUpdate) {
            if (this._map && this._lat != null && this._lng != null) {
                this._syncMarkerToProperties();
            }
        }
    }

    _initMap() {
        const el = this.querySelector('.map-picker-leaflet-pane');
        if (!el || this._map) return;

        const centerLat = this._lat ?? 41.9028;
        const centerLng = this._lng ?? 12.4964;

        this._map = L.map(el, {
            center: [centerLat, centerLng],
            zoom: this.zoom,
            zoomControl: false,
            attributionControl: false
        });

        this._layers.street = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(this._map);
        this._layers.satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });
        this._layers.topo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', { maxZoom: 17 });

        this._map.on('click', (e) => this._handleMapInteraction(e.latlng.lat, e.latlng.lng, 'click'));

        if (this._lat != null && this._lng != null) {
            this._syncMarkerToProperties();
        } else if (this.geolocateWhenEmpty || (this._lat === null && this._lng === null)) {
            void this._requestGeolocation();
        }

        this._refreshMapSize();
    }

    _refreshMapSize = () => {
        if (!this._map) {
            return;
        }

        [0, 80, 180, 350, 700].forEach((delay) => {
            setTimeout(() => {
                window.requestAnimationFrame(() => {
                    const pane = this.querySelector('.map-picker-leaflet-pane');
                    const rect = pane?.getBoundingClientRect();

                    if (!rect || rect.width === 0 || rect.height === 0) {
                        return;
                    }

                    this._map?.invalidateSize();

                    if (this._lat != null && this._lng != null) {
                        this._updateMarker(this._lat, this._lng);
                        this._map?.setView([this._lat, this._lng], Math.max(this._map.getZoom(), this.zoom), {
                            animate: false,
                        });
                    }
                });
            }, delay);
        });
    };

    _handleMapInteraction(lat, lng, source = 'manual') {
        this._isProgrammaticUpdate = true;
        const latFixed = parseFloat(lat.toFixed(6));
        const lngFixed = parseFloat(lng.toFixed(6));
        
        // Update state object directly
        if (this.state) {
            this.state.latitude = latFixed;
            this.state.longitude = lngFixed;
        }
        
        this._updateMarker(latFixed, lngFixed);
        
        this.dispatchEvent(new CustomEvent('coords-changed', {
            detail: { latitude: latFixed, longitude: lngFixed, source },
            bubbles: true,
            composed: true,
        }));

        setTimeout(() => { this._isProgrammaticUpdate = false; }, 100);
    }

    setCoordinates(lat, lng, source = 'programmatic') {
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            return;
        }

        this._handleMapInteraction(lat, lng, source);

        if (this._map) {
            this._map.setView([lat, lng], Math.max(this._map.getZoom(), 16), {
                animate: true,
            });
            this._refreshMapSize();
        }
    }

    _updateMarker(lat, lng) {
        if (!this._map) return;
        if (!this._marker) {
            this._marker = L.marker([lat, lng], { 
                draggable: true, 
                icon: createMapPickerLeafletIcon(L) 
            }).addTo(this._map);
            this._marker.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                this._handleMapInteraction(pos.lat, pos.lng, 'drag');
            });
        } else {
            this._marker.setLatLng([lat, lng]);
        }
    }

    _syncMarkerToProperties() {
        if (!this._map) return;
        const lat = this._lat;
        const lng = this._lng;
        this._updateMarker(lat, lng);
        this._map.setView([lat, lng], Math.max(this._map.getZoom(), this.zoom));
        this._refreshMapSize();
    }

    _switchLayer() {
        const layers = ['street', 'satellite', 'topo'];
        const currentIndex = layers.indexOf(this._currentLayer);
        const nextIndex = (currentIndex + 1) % layers.length;
        const nextLayer = layers[nextIndex];

        this._map.removeLayer(this._layers[this._currentLayer]);
        if (!this._layers[nextLayer]._map) {
            this._layers[nextLayer].addTo(this._map);
        }
        this._currentLayer = nextLayer;
    }

    _toggleFullscreen() {
        this.isFullscreen = !this.isFullscreen;
        
        if (this.isFullscreen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }

        this.dispatchEvent(new CustomEvent('fullscreen-changed', {
            detail: { isFullscreen: this.isFullscreen },
            bubbles: true,
            composed: true,
        }));

        if (this._map) {
            setTimeout(() => this._map?.invalidateSize(), 350);
        }
    }

    _zoomIn() {
        if (this._map) {
            this._map.zoomIn();
            setTimeout(() => this._map?.invalidateSize(), 150);
        }
    }

    _zoomOut() {
        if (this._map) {
            this._map.zoomOut();
            setTimeout(() => this._map?.invalidateSize(), 150);
        }
    }

    async _requestGeolocation() {
        if (!navigator.geolocation) return;
        this.isLocating = true;
        this.requestUpdate();

        return new Promise((resolve) => {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    this._handleMapInteraction(lat, lng, 'geolocation');
                    if (this._map) {
                        this._map.setView([lat, lng], 16);
                    }
                    this.isLocating = false;
                    this.requestUpdate();
                    resolve(true);
                },
                () => {
                    this.isLocating = false;
                    this.requestUpdate();
                    resolve(false);
                },
                { enableHighAccuracy: true, timeout: 5000 }
            );
        });
    }
}

if (!customElements.get('coordinate-picker-lit')) {
    customElements.define('coordinate-picker-lit', CoordinatePickerField);
}
