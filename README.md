# Minimal Engineer WordPress Theme

A minimal, high-performance WordPress theme designed for developers and engineers. Built with Svelte 5, Tailwind CSS, and shadcn-svelte.

## 🚀 推奨設定 (Recommended Settings)

### パーマリンク設定 (Permalinks)
WordPress のパーマリンク設定を **「投稿名」 (Post name)** にすることを強く推奨します。

### コードブロックの使い方
本テーマは PrismJS を使用した Mac ターミナル風のコードブロックをサポートしています。

- **自動判別**: コードブロックの言語設定に基づき、自動的にシンタックスハイライトと Mac 風のウィンドウ装飾が適用されます。
- **コピー機能**: ブロック右上のアイコンからワンクリックでコードをコピーできます。
- **対応言語**: TypeScript, JavaScript, Rust, Python, Go, C++, SQL など多数の言語に対応しています。

---

## 📝 投稿テンプレートの使い方 (How to Use Templates)

記事ごとに表示形式（アプリ紹介、リリースノート等）を切り替えるには、投稿編集画面の右側サイドバーにある **「Theme Post Settings」** パネルを使用します。

---

## 🛠 ビルドとインストール (Build & Installation)

1. テーマディレクトリで `npm install` を実行。
2. `npm run build` で本番用アセットを生成。
3. `dist` フォルダが生成されていることを確認。
4. WordPress 管理画面からテーマを有効化。
