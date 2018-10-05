// nuxt.d.ts
/**
 * Extends interfaces in Vue.js
 */
import Vue, { ComponentOptions } from 'vue';
import ElNotificationOptions from 'element-ui'
import { Store } from 'vuex';
import { Route } from 'vue-router';

interface NuxtContext {
  isClient: boolean;
  isServer: boolean;
  isStatic: boolean;
  isDev: boolean;
  isHMR: boolean;
  route: Route;
  store: Store<any>;
  env: object;
  query: object;
  nuxtState: object;
  req: Request;
  res: Response;
  redirect: (path: String) => void;
  error: (params: { statusCode?: String; messages?: String }) => void;
  beforeNuxtRender: ({ Components, nuxtState }) => void;
}

declare module "vue/types/options" {
  interface ComponentOptions<V extends Vue> {
    layout?: string | String[];
    middleware?: string | String[];
    fetch?: (context: NuxtContext) => void;
    asyncData?: (context: NuxtContext) => void;
    key?: (to: Route) => string;
    watchQuery?: string | String[];
  }
}
