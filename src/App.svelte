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
      <Route path="/blog" component={PostList} />
      <Route path="/sitemap" component={Sitemap} />
      <Route path="/contact" component={ContactForm} />
      <Route path="/category/:slug" component={PostList} />
      <Route path="/tag/:slug" component={PostList} />
      <!-- Match any other slug as a post or page -->
      <Route path="/:slug" let:params>
         <PostDetail slug={params.slug} />
      </Route>
      <Route path="/blog/:slug" let:params>
         <PostDetail slug={params.slug} />
      </Route>
    </Router>
  </main>
  <Footer />
  <FAB />
</div>
