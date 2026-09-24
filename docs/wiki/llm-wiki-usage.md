# LLM Wiki - Usage Guide (Module: Fixcity)

Scopo: spiegare come usare la struttura LLM Wiki locale per questo modulo.

Structure within module docs/
- docs/raw/: raw source documents (PRD, meeting notes, designs)
- docs/wiki/: processed wiki pages for fast LLM consumption
- docs/index.md: local index referencing raw → wiki

Quick QMD integration (recommended):
- Install qmd: `npm install -g @tobilu/qmd`
- Create collection: `qmd collection add ./laravel/Modules/Fixcity/docs --name fixcity-docs`
- Add context: `qmd context add qmd://fixcity-docs "Fixcity module docs and design notes"`
- Embed: `qmd embed`
- Run MCP server (optional): `qmd mcp --http --port 8181 &`

Why this layout:
- docs/raw keeps canonical sources unchanged
- docs/wiki contains sharded, opinionated pages optimized for LLMs
- This mirrors project-wide policy: keep all module docs under module's docs folder

Maintenance:
- Update docs/index.md when adding new raw or wiki docs
- Use `./bashscripts/docs/generate-wiki-index.sh` (project helper) to refresh global index
