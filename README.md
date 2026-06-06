# Minimal Engineer WordPress Theme

A minimal, high-performance WordPress theme designed for developers and engineers. Built with Svelte 5, Tailwind CSS, and shadcn-svelte.

## 🚀 推奨設定 (Recommended Settings)

### パーマリンク設定 (Permalinks)
WordPress のパーマリンク設定を **「投稿名」 (Post name)** にすることを強く推奨します。

### Markdown & Mermaid の使い方
本テーマは Markdown と Mermaid.js を標準サポートしています。

- **Markdown**: 記事本文に直接 Markdown を記述できます。自動判別されますが、エディタ右側の「Theme Post Settings」から明示的にオン/オフを切り替えることも可能です。
- **Mermaid**: ブロックエディタの「コード」ブロックまたは Markdown のコードフェンスで、言語を `mermaid` に指定して記述すると、自動的に図（フローチャート、シーケンス図等）として描画されます。

例:
\```mermaid
graph TD;
    A-->B;
    A-->C;
    B-->D;
    C-->D;
\```

---

## 📝 投稿テンプレートの使い方 (How to Use Templates)

記事ごとに表示形式（アプリ紹介、リリースノート等）を切り替えるには、投稿編集画面の右側サイドバーにある **「Theme Post Settings」** パネルを使用します。

---

## 🛠 ビルドとインストール (Build & Installation)

1. テーマディレクトリで `npm install` を実行。
2. `npm run build` で本番用アセットを生成。
3. `dist` フォルダが生成されていることを確認。
4. WordPress 管理画面からテーマを有効化。
