export default async function ({ app, store }) {
  await app.$axios.$get('/v1/users/securegroup')
    .then(res => {
      store.commit('SET_SECURE_GROUP', res.data)
    })
}
