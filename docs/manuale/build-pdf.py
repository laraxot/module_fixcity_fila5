#!/usr/bin/env python3
"""Genera i PDF del manuale d'uso FixCity dai file Markdown di questa cartella.

Uso (da qualunque cartella):  python3 build-pdf.py
Richiede: pandoc, Python Playwright con Chromium.

Produce pdf/<guida>.pdf per ogni guida e pdf/manuale-fixcity.pdf con tutto il manuale.
"""

from __future__ import annotations

import re
import subprocess
import tempfile
from pathlib import Path

from playwright.sync_api import sync_playwright

HERE = Path(__file__).resolve().parent
OUT = HERE / "pdf"

# Ordine del manuale completo: (file, titolo breve per l'intestazione).
GUIDES: list[tuple[str, str]] = [
    ("panoramica.md", "Panoramica"),
    ("cittadini.md", "Cittadini"),
    ("operatori.md", "Operatori"),
    ("supervisori-e-responsabili.md", "Supervisori e responsabili"),
    ("amministratori.md", "Amministratori"),
]

CSS = """
@page { size: A4; margin: 18mm 16mm 20mm 16mm; }
:root { --ink:#1d2733; --muted:#5b6670; --line:#d5dbe1; --accent:#0b5e3b; --soft:#f3f6f8; }
* { box-sizing: border-box; }
html { font-size: 10.5pt; }
body { font-family: "Titillium Web","Segoe UI","Helvetica Neue",Arial,sans-serif; color: var(--ink); line-height: 1.5; margin: 0; }
h1 { font-size: 22pt; color: var(--accent); margin: 0 0 6mm; padding-bottom: 3mm; border-bottom: 2px solid var(--accent); }
h2 { font-size: 15pt; color: var(--accent); margin: 8mm 0 3mm; break-after: avoid; }
h3 { font-size: 12pt; margin: 6mm 0 2mm; break-after: avoid; }
p, li { orphans: 3; widows: 3; }
p { margin: 0 0 2.5mm; }
ul, ol { margin: 0 0 3mm; padding-left: 6mm; }
li { margin: 0 0 1mm; }
strong { color: #0f1a24; }
code { font-family: "DejaVu Sans Mono", Menlo, monospace; font-size: 9pt; background: var(--soft); border: 1px solid var(--line); border-radius: 3px; padding: 0 1.2mm; }
a { color: var(--accent); text-decoration: none; }
table { width: 100%; border-collapse: collapse; margin: 2mm 0 5mm; font-size: 9.5pt; break-inside: auto; }
thead { display: table-header-group; }
tr { break-inside: avoid; }
th { background: var(--accent); color: #fff; text-align: left; font-weight: 600; }
th, td { border: 1px solid var(--line); padding: 1.6mm 2.2mm; vertical-align: top; }
tbody tr:nth-child(even) td { background: var(--soft); }
figure, p:has(> img) { margin: 3mm 0 1mm; text-align: center; break-inside: avoid; }
img { max-width: 100%; max-height: 120mm; border: 1px solid var(--line); border-radius: 4px; }
figcaption { display: none; }
p > em:only-child { display: block; text-align: center; color: var(--muted); font-size: 9pt; margin-bottom: 4mm; }
.guide { break-before: page; }
.guide:first-of-type { break-before: auto; }
.cover { height: 250mm; display: flex; flex-direction: column; justify-content: center; break-after: page; }
.cover h1 { font-size: 34pt; border: 0; margin-bottom: 4mm; }
.cover p { font-size: 13pt; color: var(--muted); }
.cover ol { font-size: 12pt; margin-top: 10mm; }
"""

FRONTMATTER = re.compile(r"\A---\n.*?\n---\n", re.S)


def guide_anchor(md_file: str) -> str:
    return "guida-" + Path(md_file).stem


def markdown_body(md_file: str, *, combined: bool) -> str:
    """Testo Markdown senza frontmatter, con i link alle altre guide adattati alla destinazione."""
    text = FRONTMATTER.sub("", (HERE / md_file).read_text(encoding="utf-8"))
    for other, _ in GUIDES:
        target = f"#{guide_anchor(other)}" if combined else other.replace(".md", ".pdf")
        text = text.replace(f"]({other})", f"]({target})")
    return text


def to_html(markdown: str) -> str:
    result = subprocess.run(
        ["pandoc", "--from=gfm+pipe_tables", "--to=html5", "--wrap=none"],
        input=markdown, capture_output=True, text=True, check=True,
    )
    return result.stdout


def page(title: str, body: str) -> str:
    return (
        "<!doctype html><html lang='it'><head><meta charset='utf-8'>"
        f"<base href='{HERE.as_uri()}/'><title>{title}</title><style>{CSS}</style></head>"
        f"<body>{body}</body></html>"
    )


def footer(label: str) -> str:
    return (
        "<div style='width:100%;font-size:8pt;color:#5b6670;padding:0 16mm;display:flex;justify-content:space-between;"
        "font-family:Arial,sans-serif'>"
        f"<span>Manuale d'uso FixCity · {label}</span>"
        "<span>Pagina <span class='pageNumber'></span> di <span class='totalPages'></span></span></div>"
    )


def print_pdf(browser, html: str, target: Path, label: str) -> None:
    with tempfile.NamedTemporaryFile("w", suffix=".html", dir=HERE, delete=False, encoding="utf-8") as tmp:
        tmp.write(html)
        tmp_path = Path(tmp.name)
    try:
        tab = browser.new_page()
        tab.goto(tmp_path.as_uri(), wait_until="networkidle")
        tab.pdf(
            path=str(target), format="A4", print_background=True, display_header_footer=True,
            header_template="<div></div>", footer_template=footer(label),
            margin={"top": "18mm", "bottom": "20mm", "left": "16mm", "right": "16mm"},
        )
        tab.close()
    finally:
        tmp_path.unlink(missing_ok=True)


def main() -> None:
    OUT.mkdir(exist_ok=True)
    with sync_playwright() as playwright:
        browser = playwright.chromium.launch()

        for md_file, label in GUIDES:
            html = to_html(markdown_body(md_file, combined=False))
            target = OUT / md_file.replace(".md", ".pdf")
            print_pdf(browser, page(label, html), target, label)
            print(f"ok {target.relative_to(HERE)}")

        cover = (
            "<section class='cover'><h1>Manuale d'uso FixCity</h1>"
            "<p>Segnalazioni dei cittadini al Comune: guide per ogni tipo di utente.</p><ol>"
            + "".join(f"<li><a href='#{guide_anchor(f)}'>{label}</a></li>" for f, label in GUIDES)
            + "</ol></section>"
        )
        sections = "".join(
            f"<section class='guide' id='{guide_anchor(f)}'>{to_html(markdown_body(f, combined=True))}</section>"
            for f, _ in GUIDES
        )
        target = OUT / "manuale-fixcity.pdf"
        print_pdf(browser, page("Manuale d'uso FixCity", cover + sections), target, "Manuale completo")
        print(f"ok {target.relative_to(HERE)}")
        browser.close()


if __name__ == "__main__":
    main()
