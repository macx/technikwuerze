import { existsSync, readFileSync } from 'node:fs'
import { resolve } from 'node:path'
import { describe, expect, it } from 'vitest'

const root = process.cwd()
const atRoot = (relativePath: string) => resolve(root, relativePath)

describe('Project smoke checks', () => {
  it('contains required workflows', () => {
    expect(existsSync(atRoot('.github/workflows/test.yml'))).toBe(true)
    expect(existsSync(atRoot('.github/workflows/deploy.yml'))).toBe(true)
  })

  it('contains required Kirby templates and blueprints', () => {
    expect(existsSync(atRoot('site/templates/episode.php'))).toBe(true)
    expect(existsSync(atRoot('site/templates/mediathek.php'))).toBe(true)
    expect(existsSync(atRoot('site/blueprints/pages/episode.yml'))).toBe(true)
    expect(existsSync(atRoot('site/blueprints/pages/participant.yml'))).toBe(true)
  })

  it('keeps release-it config with develop branch guard', () => {
    const configRaw = readFileSync(atRoot('.release-it.json'), 'utf8')
    const config = JSON.parse(configRaw) as {
      git?: { requireBranch?: string }
      hooks?: { ['before:init']?: string }
    }

    expect(config.git?.requireBranch).toBe('develop')
    expect(config.hooks?.['before:init']).toContain('pnpm run test')
  })
})
