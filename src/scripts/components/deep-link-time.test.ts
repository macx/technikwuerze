import { describe, expect, it } from 'vitest'
import { buildTimeUrl, parseTimeHash } from './deep-link-time'

describe('parseTimeHash', () => {
  it('reads whole and fractional seconds', () => {
    expect(parseTimeHash('#t=754')).toBe(754)
    expect(parseTimeHash('#t=754.9')).toBe(754)
    expect(parseTimeHash('#t=0')).toBe(0)
  })

  it('ignores everything else', () => {
    expect(parseTimeHash('')).toBeNull()
    expect(parseTimeHash('#comment-1')).toBeNull()
    expect(parseTimeHash('#t=')).toBeNull()
    expect(parseTimeHash('#t=-5')).toBeNull()
    expect(parseTimeHash('#t=12:30')).toBeNull()
    expect(parseTimeHash('#x&t=12')).toBeNull()
  })
})

describe('buildTimeUrl', () => {
  const base = { origin: 'https://technikwuerze.de', pathname: '/mediathek/folge', search: '' }

  it('appends the floored time as fragment', () => {
    expect(buildTimeUrl(base, 754.8)).toBe('https://technikwuerze.de/mediathek/folge#t=754')
  })

  it('keeps the query string', () => {
    expect(buildTimeUrl({ ...base, search: '?a=1' }, 5)).toBe(
      'https://technikwuerze.de/mediathek/folge?a=1#t=5'
    )
  })

  it('omits the fragment at the very start', () => {
    expect(buildTimeUrl(base, 0.4)).toBe('https://technikwuerze.de/mediathek/folge')
  })
})
