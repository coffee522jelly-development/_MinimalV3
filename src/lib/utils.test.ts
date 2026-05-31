import { describe, it, expect } from 'vitest';

function calculateReadingTime(content: string) {
    const text = content.replace(/<[^>]*>/g, '');
    const minutes = Math.ceil(text.length / 500);
    return minutes;
}

describe('Reading Time Calculation', () => {
    it('calculates 1 minute for short text', () => {
        expect(calculateReadingTime('<p>Hello world</p>')).toBe(1);
    });

    it('calculates 2 minutes for 600 characters', () => {
        const longText = 'a'.repeat(600);
        expect(calculateReadingTime(`<p>${longText}</p>`)).toBe(2);
    });

    it('ignores HTML tags', () => {
        const content = '<div>' + '<span>a</span>'.repeat(100) + '</div>';
        // content length with tags is > 500, but text length is 100
        expect(calculateReadingTime(content)).toBe(1);
    });
});
