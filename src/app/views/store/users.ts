import { setToken } from '~/helper/auth';

export const state = () => ({
  data: [],
  query: {},
  formSource: {},
  totalItems: 0,
  recordPerPage: 0
})

export const mutations = {
  SET_DATA(state, response) {
    state.data = response.data || null
    state.totalItems =
      typeof response.meta !== 'undefined'
        ? response.meta.totalItems
        : 0
    state.recordPerPage =
      typeof response.meta !== 'undefined'
        ? response.meta.recordPerPage
        : 0
  },

  SET_QUERY(state, response) {
    state.query.orderby =
      typeof response.meta !== 'undefined'
        ? response.meta.orderBy.toLowerCase()
        : 'id'
    state.query.ordertype =
      typeof response.meta !== 'undefined'
        ? response.meta.orderType.toLowerCase()
        : 'desc'
    state.query.page =
      typeof response.meta !== 'undefined' ? response.meta.page : 1
  },

  SET_FORM_SOURCE(state, response) {
    state.formSource = response.data || null
  },

  UPDATE_DATA(state, editedItem) {
    const index = state.data.findIndex(item => item.id === editedItem.id);
    state.data.splice(index, 1, editedItem)
  },

  DELETE_DATA(state, removedItem) {
    const index = state.data.findIndex(item => item.id === removedItem.id);
    state.data.splice(index, 1)
    state.totalItems = state.totalItems - 1
  }
}

export const actions = {
  async get_all({ commit }, { query }) {
    return await this.$axios.$get(`/v1/users`, {
      params: query
    }).then(res => {
      commit('SET_DATA', res)
      commit('SET_QUERY', res)
    });
  },

  async get_form_source({ commit }) {
    return await this.$axios.$get(`/v1/users/formsource`)
      .then(res => commit('SET_FORM_SOURCE', res));
  },

  async bulk({ commit }, { formData }) {
    return await this.$axios.$post(`/v1/users/bulk`, formData);
  },

  async add({ commit }, { formData }) {
    return await this.$axios.$post(`/v1/users`, formData);
  },

  async get({ commit }, { id }) {
    return await this.$axios.$get(`/v1/users/${id}`);
  },

  async update({ commit }, { id, formData }) {
    return await this.$axios.$put(`/v1/users/${id}`, formData)
      .then(res => commit('UPDATE_DATA', res.data));
  },

  async delete({ commit }, { id }) {
    return await this.$axios.$delete(`/v1/users/${id}`)
      .then(res => commit('DELETE_DATA', res.data));
  },

  async change_password({ commit }, { formData }) {
    return await this.$axios.$put(`/v1/users/changepassword`, formData)
      .then(res => setToken(res.data));
  },

  async login_by_email({ commit }, formData) {
    return await this.$axios.$post('/v1/users/login/email', {
      email: formData.email,
      password: formData.password
    }).then(res => setToken(res.data))
  },
}
