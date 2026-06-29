import { writeFileSync } from 'node:fs'
import { fileURLToPath } from 'node:url'
import { profaneWords } from '@2toad/profanity/dist/data/profane-words.js'

const outputPath = fileURLToPath(
  new URL('../site/plugins/technikwuerze/data/search-blacklist.de.json', import.meta.url)
)

const words = [...profaneWords.get('de')].sort()

writeFileSync(outputPath, JSON.stringify(words, null, 2) + '\n')

console.log(`Wrote ${words.length} entries to ${outputPath}`)
