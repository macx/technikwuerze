import { describe, expect, it } from 'vitest'
import {
  alignTokens,
  findActiveIndex,
  normalizeWord,
  selectWindow,
  type ArchiveWord,
} from './transcript-words-align'

const words = (entries: Array<[string, number]>): Array<ArchiveWord> =>
  entries.map(([text, time]) => ({ time, norm: normalizeWord(text) }))

describe('normalizeWord', () => {
  it('lowercases and strips punctuation', () => {
    expect(normalizeWord('Technikwürze,')).toBe('technikwürze')
    expect(normalizeWord('–')).toBe('')
  })
})

describe('alignTokens', () => {
  const archive = words([
    ['Hallo', 1000],
    ['und', 1400],
    ['herzlich', 1800],
    ['willkommen', 2200],
  ])

  it('assigns times of matching words', () => {
    const tokens = ['hallo', 'und', 'herzlich', 'willkommen'].map(normalizeWord)
    expect(alignTokens(tokens, archive, 1000, 3000)).toEqual([1000, 1400, 1800, 2200])
  })

  it('interpolates tokens that have no archive match', () => {
    const tokens = ['hallo', '156', 'herzlich'].map(normalizeWord)
    expect(alignTokens(tokens, archive, 1000, 3000)).toEqual([1000, 1400, 1800])
  })

  it('keeps times monotonic and falls back to the segment bounds', () => {
    const times = alignTokens(['x', 'y'], [], 1000, 2000)
    expect(times[0]).toBeGreaterThanOrEqual(1000)
    expect(times[1]).toBeGreaterThanOrEqual(times[0])
    expect(times[1]).toBeLessThanOrEqual(2000)
  })

  it('does not jump over far-away repeated words', () => {
    const noisy = words([
      ['a', 0],
      ['b', 100],
      ['c', 200],
      ['d', 300],
      ['e', 400],
      ['f', 500],
      ['g', 600],
      ['h', 700],
      ['i', 800],
      ['hallo', 900],
    ])
    expect(alignTokens(['hallo', 'a'], noisy, 0, 1000)[0]).toBeLessThan(900)
  })
})

describe('selectWindow', () => {
  it('limits words to the segment span', () => {
    const archive = words([
      ['a', 0],
      ['b', 1000],
      ['c', 2000],
      ['d', 4000],
    ])
    expect(selectWindow(archive, 1000, 3000).map((w) => w.norm)).toEqual(['b', 'c', 'd'])
  })
})

describe('findActiveIndex', () => {
  it('returns the last token that has started', () => {
    expect(findActiveIndex([100, 200, 300], 250)).toBe(1)
    expect(findActiveIndex([100, 200, 300], 50)).toBe(-1)
    expect(findActiveIndex([100, 200, 300], 900)).toBe(2)
  })
})
