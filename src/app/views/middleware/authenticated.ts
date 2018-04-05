export default function({ store, route, redirect, next }) {
  const routePath = route.path
  const loggedUser = store.getters.loggedUser
  const loggedToken = store.getters.loggedToken

  let redirectUrl = '/'

  if (routePath.indexOf('/admin') !== -1) {
    redirectUrl = '/admin/login'
  }

  if (!store.getters.isAuthenticated) {
    const redirectEncodeUrl = new Buffer(routePath).toString('base64')
    return redirect(`${redirectUrl}?redirect=${redirectEncodeUrl}`)
  } else {

  }
}
