declare global {
  interface Window {
    wpData: {
      root: string;
      nonce: string;
      siteName: string;
      base: string;
    };
  }
}

export function getRestUrl(path: string): string {
  const root = window.wpData?.root || '/wp-json/';
  // Ensure path doesn't start with a slash if root ends with one
  const cleanPath = path.startsWith('/') ? path.substring(1) : path;
  return `${root}${cleanPath}`;
}

export function getNonce(): string {
  return window.wpData?.nonce || '';
}

export function getBase(): string {
  return window.wpData?.base || '/';
}

const apiCache = new Map<string, { data: any, timestamp: number }>();
const CACHE_TTL = 1000 * 60 * 5; // 5 minutes

export async function fetchWithCache(url: string, options?: RequestInit): Promise<any> {
  const cacheKey = url;
  const now = Date.now();

  if (apiCache.has(cacheKey)) {
    const entry = apiCache.get(cacheKey)!;
    if (now - entry.timestamp < CACHE_TTL) {
      return entry.data;
    }
  }

  const response = await fetch(url, options);
  if (!response.ok) throw new Error(`API error: ${response.statusText}`);

  const data = await response.json();
  apiCache.set(cacheKey, { data, timestamp: now });
  return data;
}
