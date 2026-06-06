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
