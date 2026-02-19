import { describe, it, expect } from 'vitest'

describe('Basic TypeScript functionality', () => {
  it('should support TypeScript types', () => {
    const message: string = 'Hello, TypeScript!'
    expect(message).toBe('Hello, TypeScript!')
  })

  it('should support modern JavaScript features', () => {
    const numbers = [1, 2, 3, 4, 5]
    const doubled = numbers.map((n) => n * 2)
    expect(doubled).toEqual([2, 4, 6, 8, 10])
  })

  it('should support async/await', async () => {
    const promise = Promise.resolve('success')
    const result = await promise
    expect(result).toBe('success')
  })
})
