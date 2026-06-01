# Minimal Engineer WordPress Theme

A minimal, high-performance WordPress theme designed for developers and engineers. Built with Svelte 5, Tailwind CSS, and shadcn-svelte.

## 🚀 推奨設定 (Recommended Settings)

### パーマリンク設定 (Permalinks)
このテーマは Single Page Application (SPA) として動作するため、WordPress のパーマリンク設定を **「投稿名」 (Post name)** にすることを強く推奨します。

- **設定方法**: WordPress 管理画面 > 設定 > パーマリンク > 「投稿名」を選択して保存。

これにより、`example.com/my-post` のようなクリーンな URL で記事にアクセスできるようになります。

---

## 🏗 ページ構造とルーティング (Structure & Routing)

本テーマはフロントエンドを Svelte で構築しており、WordPress REST API を介してコンテンツを取得します。

### ルーティングロジック
- `/` : 記事一覧 (Post List)
- `/blog` : 記事一覧
- `/blog/:slug` : 記事詳細
- `/category/:slug` : カテゴリ別一覧
- `/tag/:slug` : タグ別一覧
- `/sitemap` : サイトマップ
- `/contact` : お問い合わせフォーム
- `/:slug` : 固定ページ (Page) または 記事詳細 (Post) を自動判別して表示

### 階層構造
- **ナビゲーション**: WordPress の「固定ページ」の親子関係を自動的に取得し、ヘッダーにドロップダウン、フッターにネストされたリストとして表示します。
- **カテゴリ**: 記事詳細ページのサイドバーに、最大3階層までのディレクトリツリー形式でカテゴリを表示します。

---

## 📝 投稿テンプレート (Templates)

本テーマは「投稿」のメタデータ (`_me_template_type`) を切り替えることで、用途に合わせた4つの表示形式をサポートしています。

### 1. 通常記事 (Standard)
- **用途**: 技術ブログ、チュートリアル、コラム。
- **特徴**: シンプルなタイポグラフィ重視のレイアウト。

### 2. アプリ紹介 (App Introduction)
- **用途**: 個人開発アプリ、SaaS、ポートフォリオ。
- **メタデータ**:
  - `_me_app_subtitle`: アプリのキャッチコピー
  - `_me_app_link_web`: ウェブサイト URL
  - `_me_app_link_github`: GitHub URL
  - `_me_app_logo_id`: ロゴ画像 (メディアID)

### 3. リリースノート (Release Notes)
- **用途**: アップデート情報の記録。
- **メタデータ**:
  - `_me_release_version`: バージョン番号 (例: 1.0.0)

### 4. 開発日記 (Development Diary)
- **用途**: 開発ログ、日報。
- **メタデータ**:
  - `_me_diary_date`: 開発日
  - `_me_diary_hours`: 作業時間

---

## 💻 開発者向け機能 (Developer Features)

- **コードブロック**: Mac ターミナル風のデザイン。コピーボタンと言語名表示を搭載。
- **ダークモード**: OS設定およびブラウザ保存に対応。
- **読了目安**: 文字数に基づき自動計算 (500文字/分)。
- **目次 (TOC)**: 本文中の H2, H3, H4 を自動抽出し、スクロール追従する目次を表示。
- **タイポグラフィ**: `@tailwindcss/typography` (prose) により、表やリスト、引用を美しく表示。

---

## 🛠 ビルドとインストール (Build & Installation)

1. テーマディレクトリで `npm install` を実行。
2. `npm run build` で本番用アセットを生成。
3. `dist` フォルダが生成されていることを確認。
4. WordPress 管理画面からテーマを有効化。

*注意: 開発環境では REST API のエンドポイントが WordPress 側で動作している必要があります。*
