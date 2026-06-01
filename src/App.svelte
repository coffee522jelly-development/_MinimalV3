<script lang="ts">
  import { onMount } from 'svelte';
  import { Router, Route } from "svelte-routing";
  import Header from "./lib/components/Header.svelte";
  import Footer from "./lib/components/Footer.svelte";
  import FAB from "./lib/components/FAB.svelte";
  import PostList from "./lib/components/PostList.svelte";
  import PostDetail from "./lib/components/PostDetail.svelte";
  import Sitemap from "./lib/components/Sitemap.svelte";
  import ContactForm from "./lib/components/ContactForm.svelte";
  import './app.css';

  export let url = "";
</script>

<div class="min-h-screen flex flex-col bg-background text-foreground">
  <Header />
  <main class="flex-1">
    <Router {url}>
      <Route path="/">
         <PostList />
      </Route>
      <Route path="/blog">
         <PostList />
      </Route>
      <Route path="/category/:slug" let:params>
         <PostList slug={params.slug} listType="category" />
      </Route>
      <Route path="/tag/:slug" let:params>
         <PostList slug={params.slug} listType="tag" />
      </Route>
      <Route path="/sitemap">
         <Sitemap />
      </Route>
      <Route path="/contact">
         <ContactForm />
      </Route>
      <!-- Handling potentially multi-segment slugs for hierarchical pages -->
      <Route path="/*" let:params>
         <PostDetail slug={params['*']} />
      </Route>
    </Router>
  </main>
  <Footer />
  <FAB />
</div>
