import { getLocaleFromLocalStorage } from '~/helper/locale'

export default function({ app, store, req }) {

  // Get locale from cookie
  let locale = getLocaleFromLocalStorage()

  // Set locale
  store.commit('SET_LANG', locale)
  app.i18n.locale = store.state.locale
}
