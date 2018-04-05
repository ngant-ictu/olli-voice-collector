import Cookie from 'js-cookie'

export const setLocale = locale => {
  window.localStorage.setItem('locale', locale)
  Cookie.set('locale', locale, { expires: 365 })
}

export const getLocaleFromLocalStorage = () => {
  const locale = window.localStorage.locale
  return locale ? locale : 'en'
}
