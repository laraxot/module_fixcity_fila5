---
title: "Deep Dive: ML Algorithms - Duplicate Detection & Priority Scoring"
type: technical-spec
tags: [fixcity, ml, ai, duplicate-detection, computer-vision, nlp, algorithms]
status: draft
created: 2026-06-17
---

# Deep Dive: ML Algorithms & AI Implementation

## 1. Duplicate Detection Algorithm

### 1.1 Problem Definition

**Input:** Nuovo ticket $T_{new}$ con attributi:
- Immagine $I_{new}$ (opzionale)
- Descrizione testo $D_{new}$
- Coordinate GPS $(lat_{new}, lng_{new})$
- Timestamp $t_{new}$
- Categoria $c_{new}$

**Output:** Lista di candidati duplicati ordinati per confidence score:
$$[(T_1, s_1), (T_2, s_2), ..., (T_n, s_n)]$$
dove $s_i \\\in [0, 1]$ è similarity score.

**Threshold:** $s_{threshold} = 0.85$ per flag automatico, $[0.70, 0.85)$ per review umana.

---

### 1.2 Multi-Modal Similarity Fusion

#### Formula Generale

$$S_{total} = \\sum_{i} w_i \\cdot S_i$$

dove:
- $S_{image}$: Similarità immagini (0-1)
- $S_{text}$: Similarità descrizioni (0-1)  
- $S_{time}$: Proximity temporale (0-1)
- $S_{location}$: Proximity spaziale (0-1)
- $S_{category}$: Match categoria (0 o 1)

**Pesi ottimizzati** (da training su dataset storico):
```
w_image = 0.40    (se entrambe le immagini presenti)
w_text = 0.25     (se entrambe le descrizioni presenti)
w_time = 0.15
w_location = 0.15
w_category = 0.05
```

**Adaptive weighting:** Se $I_{new} = null$, redistribuire $w_{image}$ su $w_{text}$ e $w_{location}$.

---

### 1.3 Image Similarity: Perceptual Hash (pHash) + CNN Embeddings

#### Step 1: Perceptual Hash (pHash) - Fast Pre-filtering

**Algoritmo pHash:**
1. Resize a 32x32 pixel
2. Convert to grayscale
3. Apply DCT (Discrete Cosine Transform)
4. Take top-left 8x8 frequencies (low freq = perceptual features)
5. Compute mean value
6. Generate 64-bit hash: 1 if > mean, 0 if < mean

```python
def compute_phash(image_path):
    # 1. Resize
    img = Image.open(image_path).convert('L').resize((32, 32))
    
    # 2. DCT
    pixels = np.array(img, dtype=np.float32)
    dct = cv2.dct(pixels)
    
    # 3. Top-left 8x8
    dct_low = dct[:8, :8]
    
    # 4. Mean (escludendo DC component [0,0])
    avg = (dct_low.sum() - dct_low[0, 0]) / 63
    
    # 5. Hash
    hash_bits = (dct_low > avg).flatten().astype(int)
    hash_string = ''.join(map(str, hash_bits))
    
    return hash_string
```

**Hamming Distance:**
$$d_{hamming} = \\sum_{i=0}^{63} |h_1[i] - h_2[i]|$$

**Conversione a similarity:**
$$S_{phash} = 1 - \\frac{d_{hamming}}{64}$$

**Filtering:** Se $S_{phash} < 0.50$, scartare candidato (troppo diverso).

---

#### Step 2: Deep Learning Embeddings (ResNet50) - Fine-grained Similarity

**Architettura:**
```
Input Image (224x224x3)
    ↓
ResNet50 (pre-trained on ImageNet, fine-tuned on civic images)
    ↓
Global Average Pooling
    ↓
Dense Layer (512 units, ReLU)
    ↓
L2 Normalization
    ↓
Embedding Vector (512-d)
```

**Training Data:**
- 50,000+ immagini da civic datasets
- 10,000 coppie etichettate (duplicate / non-duplicate)
- Data augmentation: rotation, brightness, crop, blur

**Similarity Cosine:**
$$S_{cnn} = \\frac{\\vec{e_1} \\cdot \\vec{e_2}}{||\\vec{e_1}|| \\cdot ||\\vec{e_2}||}$$

**Combined Image Score:**
$$S_{image} = \\alpha \\cdot S_{phash} + (1 - \\alpha) \\cdot S_{cnn}$$

dove $\\alpha = 0.3$ (pHash veloce, CNN accurato).

---

### 1.4 Text Similarity: Sentence-BERT + TF-IDF

#### Step 1: TF-IDF Baseline (Fast)

**Preprocessing:**
1. Lowercase
2. Remove punctuation, numbers
3. Tokenization (Italian spaCy)
4. Remove stopwords (italian)
5. Lemmatization

**TF-IDF Vectors:**
$$\\vec{v} = [tf-idf(t_1), tf-idf(t_2), ..., tf-idf(t_n)]$$

**Cosine Similarity:**
$$S_{tfidf} = \\cos(\\theta) = \\frac{\\vec{v_1} \\cdot \\vec{v_2}}{||\\vec{v_1}|| \\cdot ||\\vec{v_2}||}$$

---

#### Step 2: Sentence-BERT Embeddings (Semantic)

**Modello:** `paraphrase-multilingual-mpnet-base-v2` (multilingual, 768-d)

Fine-tuning su dataset italiano:
```python
from sentence_transformers import SentenceTransformer, InputExample, losses

model = SentenceTransformer('paraphrase-multilingual-mpnet-base-v2')

# Training pairs
train_examples = [
    InputExample(texts=["buca grande in via Roma", "voragine strada Roma"], label=0.9),
    InputExample(texts=["buca in via Roma", "spazzatura non raccolta"], label=0.1),
    # ... 10,000 esempi
]

train_loss = losses.CosineSimilarityLoss(model)
model.fit(train_objectives=[(train_dataloader, train_loss)], epochs=3)
```

**Embedding similarity:**
$$S_{bert} = \\cos(\\vec{e_1}, \\vec{e_2})$$

**Combined Text Score:**
$$S_{text} = \\beta \\cdot S_{tfidf} + (1 - \\beta) \\cdot S_{bert}$$

dove $\\beta = 0.2$ (BERT domina per semantica).

---

### 1.5 Temporal Similarity

**Funzione decrescente esponenziale:**

$$S_{time} = e^{-\\lambda \\cdot \\Delta t}$$

dove:
- $\\Delta t = |t_{new} - t_{old}|$ in ore
- $\\lambda = \\frac{\\ln(2)}{t_{half}}$ con $t_{half} = 24$ ore

**Esempi:**
- Stessa ora: $S_{time} = 1.0$
- 24 ore: $S_{time} = 0.5$
- 7 giorni: $S_{time} = 0.09$
- 30 giorni: $S_{time} \\approx 0$

**Hard cutoff:** Se $\\Delta t > 30$ giorni, $S_{time} = 0$ (non è duplicato).

---

### 1.6 Spatial Similarity (Geographic)

#### Haversine Distance

$$d = 2r \\cdot \\arcsin\\left(\\sqrt{\\sin^2\\left(\\frac{\\Delta \\phi}{2}\\right) + \\cos(\\phi_1) \\cos(\\phi_2) \\sin^2\\left(\\frac{\\Delta \\lambda}{2}\\right)}\\right)$$

dove:
- $r = 6,371$ km (raggio Terra)
- $\\phi$ = latitudine in radianti
- $\\lambda$ = longitudine in radianti

**Conversione a similarity:**

$$S_{location} = \\max\\left(0, 1 - \\frac{d}{d_{max}}\\right)$$

dove $d_{max} = 150$ metri.

**Esempi:**
- Stesso punto: $S_{location} = 1.0$
- 75m: $S_{location} = 0.5$
- 150m: $S_{location} = 0.0$

---

### 1.7 Complete Algorithm Pseudocode

```python
class DuplicateDetectionService:
    def __init__(self):
        self.cnn_model = load_resnet50_finetuned()
        self.bert_model = load_sentence_bert_finetuned()
        self.phash_cache = load_phash_index()  # Inverted index per fast lookup
        
        # Weights
        self.weights = {
            'image': 0.40,
            'text': 0.25,
            'time': 0.15,
            'location': 0.15,
            'category': 0.05
        }
        
        # Thresholds
        self.AUTO_DUPLICATE_THRESHOLD = 0.85
        self.REVIEW_THRESHOLD = 0.70
        self.CANDIDATE_LIMIT = 10
    
    def find_duplicates(self, new_ticket: Ticket) -> List[DuplicateCandidate]:
        # Step 1: Fast spatial filtering (PostGIS)
        nearby_candidates = self._spatial_query(
            lat=new_ticket.lat,
            lng=new_ticket.lng,
            radius_meters=150,
            time_window_days=30,
            exclude_id=new_ticket.id
        )
        
        if not nearby_candidates:
            return []
        
        # Step 2: Compute features for new ticket
        new_features = self._extract_features(new_ticket)
        
        # Step 3: Score each candidate
        results = []
        for candidate in nearby_candidates[:50]:  # Max 50 candidates
            score, factors = self._compute_similarity(
                new_features, 
                candidate,
                new_ticket
            )
            
            if score >= self.REVIEW_THRESHOLD:
                results.append(DuplicateCandidate(
                    ticket=candidate,
                    score=score,
                    factors=factors,
                    auto_flag=score >= self.AUTO_DUPLICATE_THRESHOLD
                ))
        
        # Step 4: Sort and limit
        results.sort(key=lambda x: x.score, reverse=True)
        return results[:self.CANDIDATE_LIMIT]
    
    def _extract_features(self, ticket: Ticket) -> TicketFeatures:
        features = TicketFeatures()
        
        # Image features
        if ticket.media_images:
            features.phash = compute_phash(ticket.media_images[0])
            features.cnn_embedding = self.cnn_model.encode(
                ticket.media_images[0]
            )
        
        # Text features
        if ticket.description:
            features.description_tfidf = self.tfidf_vectorizer.transform(
                [ticket.description]
            )
            features.description_bert = self.bert_model.encode(
                ticket.description
            )
        
        # Location
        features.location = (ticket.lat, ticket.lng)
        features.timestamp = ticket.created_at
        features.category = ticket.type
        
        return features
    
    def _compute_similarity(self, new: TicketFeatures, candidate: Ticket, 
                           original_ticket: Ticket) -> Tuple[float, Dict]:
        scores = {}
        
        # Image similarity (if both have images)
        if new.phash and candidate.media_images:
            candidate_phash = get_cached_phash(candidate)
            scores['phash'] = 1 - hamming_distance(new.phash, candidate_phash) / 64
            
            candidate_embedding = get_cached_cnn_embedding(candidate)
            scores['cnn'] = cosine_similarity(new.cnn_embedding, candidate_embedding)
            
            scores['image'] = 0.3 * scores['phash'] + 0.7 * scores['cnn']
        else:
            scores['image'] = None
        
        # Text similarity
        if new.description_bert and candidate.description:
            candidate_tfidf = get_cached_tfidf(candidate)
            candidate_bert = get_cached_bert_embedding(candidate)
            
            scores['tfidf'] = cosine_similarity(
                new.description_tfidf, 
                candidate_tfidf
            )
            scores['bert'] = cosine_similarity(
                new.description_bert, 
                candidate_bert
            )
            scores['text'] = 0.2 * scores['tfidf'] + 0.8 * scores['bert']
        else:
            scores['text'] = None
        
        # Time similarity
        delta_hours = abs(
            (original_ticket.created_at - candidate.created_at).total_seconds() / 3600
        )
        scores['time'] = math.exp(-math.log(2) * delta_hours / 24)
        
        # Location similarity
        distance_meters = haversine_distance(
            new.location[0], new.location[1],
            candidate.lat, candidate.lng
        )
        scores['location'] = max(0, 1 - distance_meters / 150)
        
        # Category
        scores['category'] = 1.0 if original_ticket.type == candidate.type else 0.0
        
        # Compute weighted total
        total_score = 0.0
        total_weight = 0.0
        
        for factor, weight in self.weights.items():
            if scores[factor] is not None:
                total_score += weight * scores[factor]
                total_weight += weight
        
        # Normalize if some factors missing
        if total_weight > 0:
            final_score = total_score / total_weight
        else:
            final_score = 0.0
        
        return final_score, scores
    
    def _spatial_query(self, lat: float, lng: float, 
                      radius_meters: int, time_window_days: int,
                      exclude_id: int) -> List[Ticket]:
        """PostGIS query for fast spatial filtering"""
        sql = """
        SELECT * FROM tickets
        WHERE ST_DWithin(
            location::geography,
            ST_SetSRID(ST_MakePoint(%s, %s), 4326)::geography,
            %s
        )
        AND created_at > NOW() - INTERVAL '%s days'
        AND id != %s
        AND is_duplicate = FALSE
        AND status NOT IN ('closed', 'rejected')
        ORDER BY created_at DESC
        LIMIT 50
        """
        return db.query(sql, [lng, lat, radius_meters, time_window_days, exclude_id])
```

---

### 1.8 Performance Optimization

#### Indexing Strategy

```sql
-- pHash index for fast lookup
CREATE INDEX idx_media_phash ON ticket_media(perceptual_hash);

-- HNSW index for vector similarity (pgvector extension)
CREATE INDEX idx_media_embedding ON ticket_media 
    USING hnsw (cnn_embedding vector_cosine_ops);

-- Composite for common queries
CREATE INDEX idx_tickets_spatial_temporal ON tickets 
    USING GIST(location) 
    INCLUDE (created_at, type, status)
    WHERE is_duplicate = FALSE;
```

#### Caching Strategy

```python
# Redis caching for expensive operations
@cache.redis(ttl=3600)
def get_cached_cnn_embedding(media_id: int) -> np.ndarray:
    return load_from_db(media_id)

@cache.redis(ttl=86400)
def get_cached_phash(media_id: int) -> str:
    return load_from_db(media_id)

# LFU cache for BERT embeddings (memory)
@cache.lfu(maxsize=10000)
def get_cached_bert_embedding(ticket_id: int) -> np.ndarray:
    return compute_bert_embedding(ticket_id)
```

#### Async Processing

```python
# Heavy computation in background job
@app.task(queue='ml', priority='high')
def detect_duplicates_async(ticket_id: int):
    ticket = Ticket.find(ticket_id)
    
    # Run duplicate detection
    candidates = duplicate_service.find_duplicates(ticket)
    
    # Auto-flag high confidence
    for candidate in candidates:
        if candidate.auto_flag:
            ticket.mark_as_duplicate_of(
                candidate.ticket, 
                confidence=candidate.score
            )
            notify_user(ticket.reporter, 'duplicate_found')
            break
        elif candidate.score >= 0.70:
            notify_operators('potential_duplicate_review', ticket, candidate)
```

---

## 2. Priority Scoring Algorithm

### 2.1 Multi-Factor Scoring Formula

$$P_{total} = \\min\\left(100, \\sum_{i} w_i^{priority} \\cdot f_i(x_i) + \\sum_{j} w_j^{urgency} \\cdot u_j(y_j)\\right)$$

**Fattori:**
1. **Questionnaire impact** (40%): Dalle risposte al questionario
2. **Location criticality** (20%): Zona sensibile (ospedale, scuola)
3. **Time sensitivity** (15%): Orario/giorno critico
4. **Category base** (15%): Peso base per tipologia
5. **User reputation** (10%): Storia reporter (falso positivi)

---

### 2.2 Questionnaire Impact Scoring

**Formula per singolo campo:**

$$f_{field} = impact_{level} \\times value_{multiplier}$$

dove:
- $impact_{level}$: mappato a punteggio
  - `none` = 0
  - `low` = 10
  - `medium` = 25
  - `high` = 50
  - `critical` = 100

**Esempio buca stradale:**

| Campo | Risposta | Impact | Formula | Punteggio |
|-------|----------|--------|---------|-----------|
| dimensione | >50cm | critical | 100 × 1.0 | 100 |
| pericolo | Sì | critical | 100 × 1.5 | 150 |
| posizione | Carreggiata | high | 50 × 1.0 | 50 |
| **Totale** | | | | **300** |

**Normalizzazione:**
$$P_{questionnaire} = \\min\\left(100, \\frac{\\sum f_{field}}{max_{theoretical}} \\times 100\\right)$$

---

### 2.3 Location Criticality

**Zone weight table:**

```sql
CREATE TABLE zone_criticality (
    zone_type VARCHAR(50) PRIMARY KEY,
    base_score INTEGER,
    multiplier DECIMAL(3,2)
);

INSERT INTO zone_criticality VALUES
    ('hospital', 80, 1.5),
    ('school', 70, 1.4),
    ('highway', 90, 1.3),
    ('pedestrian', 60, 1.2),
    ('residential', 20, 1.0),
    ('industrial', 30, 1.0),
    ('park', 10, 0.8);
```

**Geofencing query:**
```sql
SELECT zc.multiplier 
FROM zone_criticality zc
JOIN zones z ON z.type = zc.zone_type
WHERE ST_Contains(z.boundary, ticket.location);
```

---

### 2.4 Time Sensitivity

**Critical hours:**
- 07:00-09:00: +20% (ora di punta)
- 17:00-19:00: +20% (ora di punta)
- 22:00-06:00: +30% (notte, servizi ridotti)

**Critical days:**
- Weekend: +10%
- Festivi: +20%
- Condizioni meteo avverse: +30%

---

### 2.5 Complete Priority Algorithm

```python
class PriorityScoringService:
    def __init__(self):
        self.weights = {
            'questionnaire': 0.40,
            'location': 0.20,
            'time': 0.15,
            'category': 0.15,
            'user_reputation': 0.10
        }
    
    def calculate_priority(self, ticket: Ticket) -> PriorityResult:
        scores = {}
        
        # 1. Questionnaire impact
        if ticket.answers:
            scores['questionnaire'] = self._score_questionnaire(ticket.answers)
        else:
            scores['questionnaire'] = 50  # Default
        
        # 2. Location criticality
        scores['location'] = self._score_location(ticket.location)
        
        # 3. Time sensitivity
        scores['time'] = self._score_time(ticket.created_at)
        
        # 4. Category base score
        scores['category'] = self._score_category(ticket.type)
        
        # 5. User reputation
        if ticket.reporter:
            scores['user_reputation'] = self._score_user(ticket.reporter)
        else:
            scores['user_reputation'] = 50
        
        # Weighted sum
        total_score = sum(
            self.weights[factor] * score 
            for factor, score in scores.items()
        )
        
        # Cap at 100
        final_score = min(100, int(total_score))
        
        # Determine urgency level
        urgency = self._score_to_urgency(final_score)
        
        # Estimate resolution time
        estimated_hours = self._estimate_resolution(final_score, ticket.type)
        
        return PriorityResult(
            score=final_score,
            urgency=urgency,
            estimated_hours=estimated_hours,
            breakdown=scores
        )
    
    def _score_questionnaire(self, answers: List[TicketAnswer]) -> float:
        total = 0
        max_possible = 0
        
        for answer in answers:
            field = answer.questionnaireField
            impact_score = self._impact_to_score(field.priority_impact)
            
            # Value multiplier from options
            value_mult = 1.0
            if field.options:
                option = next(
                    (o for o in field.options if o['value'] == answer.value), 
                    None
                )
                if option:
                    value_mult = option.get('priority_multiplier', 1.0)
            
            # Boolean fields
            if field.type == 'boolean' and answer.value == True:
                value_mult = 1.5  # True = more urgent
            
            total += impact_score * value_mult
            max_possible += 100  # Critical max
        
        return min(100, (total / max_possible) * 100) if max_possible > 0 else 50
    
    def _score_location(self, location: Point) -> float:
        # Query zone criticality
        zone = Zone.query().filter(
            ST_Contains(Zone.boundary, location)
        ).first()
        
        if zone:
            base = zone.criticality.base_score
            mult = zone.criticality.multiplier
            return min(100, base * mult)
        
        return 30  # Default
    
    def _score_time(self, created_at: datetime) -> float:
        score = 50  # Base
        hour = created_at.hour
        weekday = created_at.weekday()
        
        # Critical hours
        if 7 <= hour <= 9 or 17 <= hour <= 19:
            score += 20
        elif hour < 6 or hour > 22:
            score += 30
        
        # Weekend
        if weekday >= 5:  # Saturday=5, Sunday=6
            score += 10
        
        return min(100, score)
    
    def _score_category(self, category: TicketTypeEnum) -> float:
        base_scores = {
            TicketTypeEnum.POTHOLE: 60,
            TicketTypeEnum.GRAFFITI: 30,
            TicketTypeEnum.STREETLIGHT: 70,
            TicketTypeEnum.GARBAGE: 50,
            TicketTypeEnum.VANDALISM: 40,
            # ... etc
        }
        return base_scores.get(category, 40)
    
    def _score_user(self, user: User) -> float:
        # Based on user's report history
        stats = user.ticket_stats()
        
        if stats.total == 0:
            return 50
        
        # Resolution rate (higher = more trustworthy)
        resolution_score = (stats.resolved / stats.total) * 100
        
        # False positive rate (lower = better)
        if stats.false_positives > 0:
            fp_penalty = min(30, stats.false_positives * 5)
        else:
            fp_penalty = 0
        
        # Account age bonus
        age_days = (now() - user.created_at).days
        age_bonus = min(10, age_days / 365 * 2)  # +2 per year, max 10
        
        return min(100, max(30, resolution_score - fp_penalty + age_bonus))
    
    def _score_to_urgency(self, score: int) -> str:
        if score >= 80:
            return 'critical'
        elif score >= 60:
            return 'high'
        elif score >= 40:
            return 'medium'
        else:
            return 'low'
    
    def _estimate_resolution(self, score: int, category: TicketTypeEnum) -> int:
        # Base hours by category
        base_hours = {
            TicketTypeEnum.POTHOLE: 48,
            TicketTypeEnum.GRAFFITI: 120,
            TicketTypeEnum.STREETLIGHT: 24,
            TicketTypeEnum.GARBAGE: 72,
        }.get(category, 72)
        
        # Adjust by priority
        # Higher priority = faster resolution target
        if score >= 80:
            return int(base_hours * 0.25)  # 4x faster
        elif score >= 60:
            return int(base_hours * 0.5)
        elif score >= 40:
            return base_hours
        else:
            return int(base_hours * 2)
```

---

## 3. Model Training & Evaluation

### 3.1 Training Data Requirements

**Duplicate Detection:**
- 50,000+ labeled pairs (duplicate/non-duplicate)
- Stratified by: category, lighting conditions, image quality
- Balanced: 50% duplicate, 50% non-duplicate

**Priority Scoring:**
- 100,000+ tickets with expert-labeled priority
- Include edge cases (rare but critical scenarios)

### 3.2 Evaluation Metrics

**Duplicate Detection:**
```python
# Precision@k
precision_at_5 = true_positives_in_top_5 / 5

# mAP (mean Average Precision)
def average_precision(candidates, ground_truth):
    precisions = []
    true_positives = 0
    
    for i, candidate in enumerate(candidates, 1):
        if candidate.ticket_id in ground_truth:
            true_positives += 1
            precisions.append(true_positives / i)
    
    return sum(precisions) / len(ground_truth) if ground_truth else 0

# Confusion Matrix
def evaluate(predictions, ground_truth, threshold=0.85):
    tp = fp = tn = fn = 0
    
    for pred, truth in zip(predictions, ground_truth):
        is_duplicate = pred['score'] >= threshold
        
        if is_duplicate and truth['is_duplicate']:
            tp += 1
        elif is_duplicate and not truth['is_duplicate']:
            fp += 1
        elif not is_duplicate and not truth['is_duplicate']:
            tn += 1
        else:
            fn += 1
    
    precision = tp / (tp + fp) if (tp + fp) > 0 else 0
    recall = tp / (tp + fn) if (tp + fn) > 0 else 0
    f1 = 2 * (precision * recall) / (precision + recall) if (precision + recall) > 0 else 0
    
    return {'precision': precision, 'recall': recall, 'f1': f1}
```

**Target Performance:**
- Precision ≥ 95% (evitare falsi positivi)
- Recall ≥ 85% (catturare la maggior parte dei duplicati)
- F1 ≥ 90%
- Inference time < 200ms per ticket

---

*Algorithm design based on: computer vision best practices, information retrieval theory, geospatial analysis, and civic tech domain expertise.*
