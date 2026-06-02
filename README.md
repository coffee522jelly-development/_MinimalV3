# Minimal Engineer WordPress Theme

A minimal, high-performance WordPress theme designed for developers and engineers. Built with Svelte 5, Tailwind CSS, and shadcn-svelte.

## 🚀 推奨設定 (Recommended Settings)

### パーマリンク設定 (Permalinks)
このテーマは Single Page Application (SPA) として動作するため、WordPress のパーマリンク設定を **「投稿名」 (Post name)** にすることを強く推奨します。

- **設定方法**: WordPress 管理画面 > 設定 > パーマリンク > 「投稿名」を選択して保存。

---

## 📝 投稿テンプレートの使い方 (How to Use Templates)

記事ごとに表示形式（アプリ紹介、リリースノート等）を切り替えるには、投稿編集画面の右側サイドバーにある **「Theme Post Settings」** パネルを使用します。

### 設定項目
1. **Template Type**: 以下の4つから選択できます。
    - **Tech (Standard)**: 通常の技術記事。
    - **App Intro**: アプリ紹介カードを上部に表示。Subtitle, Website URL, GitHub URL が設定可能。
    - **Release Notes**: バージョン番号を表示。
    - **Dev Diary**: 開発ログ（日付と時間）を表示。
2. **各専用フィールド**: 選択したタイプに応じて必要な入力項目が表示されます。

*※ 手動でカスタムフィールドを入力する必要はありません。このパネルで入力した内容は自動的に保存され、フロントエンドに反映されます。*

---

## 🏗 ページ構造とルーティング (Structure & Routing)

### ルーティングロジック
- `/` : 記事一覧 (Post List)
- `/category/:slug` : カテゴリ別一覧
- `/tag/:slug` : タグ別一覧
- `/sitemap` : サイトマップ
- `/contact` : お問い合わせフォーム
- `/*` : 固定ページ (Page) または 記事詳細 (Post) を自動判別して表示

---

## 💻 開発者向け機能 (Developer Features)

- **コードブロック**: Mac ターミナル風デザイン（シンタックスハイライト、コピーボタン付）。
- **3カラムレイアウト**: 広画面では左にカテゴリツリー、右に目次(TOC)を表示。
- **ダークモード**: OS設定およびブラウザ保存に対応。
- **UIラベルのカスタマイズ**: 管理画面の「カスタマイズ」から、目次やカテゴリのラベル名を自由に変更可能。

---

## 🛠 ビルドとインストール (Build & Installation)

1. テーマディレクトリで `npm install` を実行。
2. `npm run build` で本番用アセットを生成。
3. `dist` フォルダが生成されていることを確認。
4. WordPress 管理画面からテーマを有効化。
