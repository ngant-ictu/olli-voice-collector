import {
  getUserFromLocalStorage,
  getTokenFromLocalStorage
} from '~/helper/auth'

export default function({ store, route, redirect, next }) {
  const loggedUser = getUserFromLocalStorage()
  const loggedToken = getTokenFromLocalStorage()
  // const secureGroup = store.state.secureGroup
  // const routePath = route.path

  store.commit('SET_USER', {
    user: loggedUser,
    token: loggedToken
  })

  // let redirectUrl = '/'
  // if (routePath === '/admin') {
  //   redirectUrl = '/admin/login'
  // }
  //
  // if (!store.getters.isAuthenticated) {
  //   const redirectEncodeUrl = new Buffer(routePath).toString('base64')
  //   redirect('/admin/login')
  //   // return redirect(`${redirectUrl}?redirect=${redirectEncodeUrl}`)
  // } else {
  //   if (secureGroup.indexOf(loggedUser.sub.groupid) !== -1) {
  //     // return next
  //   } else {
  //     // return redirect('/403')
  //   }
  // }
}
