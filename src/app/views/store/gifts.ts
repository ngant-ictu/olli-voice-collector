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
  },

  UPDATE_ITEMS(state, clonedItem) {
    state.data.push(clonedItem)
  }
}

export const actions = {
  async get_all({ commit }, { query }) {
    return await this.$axios.$get(`/v1/gifts`, {
      params: query
    }).then(res => {
      commit('SET_DATA', res)
      commit('SET_QUERY', res)
    });
  },

  async get_form_source({ commit }) {
    return await this.$axios.$get(`/v1/gifts/formsource`)
      .then(res => commit('SET_FORM_SOURCE', res));
  },

  async bulk({ commit }, { formData }) {
    return await this.$axios.$post(`/v1/gifts/bulk`, formData);
  },

  async add({ commit }, { formData }) {
    let data = new FormData();
    formData.files.map((item, index) => {
      data.append(`files[${index}][value]`, item.raw);
    });
    data.append('attrs', JSON.stringify(formData.attrs));
    data.append('name', formData.name);
    data.append('requiredpoint', formData.requiredpoint);
    data.append('type', formData.type);

    return await this.$axios.$post(`/v1/gifts`, data)
      .then(res => commit('UPDATE_ITEMS', res.data));
  },

  async get({ commit }, { id }) {
    return await this.$axios.$get(`/v1/gifts/${id}`);
  },

  async update({ commit }, { id, formData }) {
    return await this.$axios.$put(`/v1/gifts/${id}`, formData)
      .then(res => commit('UPDATE_DATA', res.data));
  },

  async clone({ commit }, { id, formData }) {
    return await this.$axios.$post(`/v1/gifts/${id}/clone`, formData)
      .then(res => commit('UPDATE_ITEMS', res.data));
  },

  async delete({ commit }, { id }) {
    return await this.$axios.$delete(`/v1/gifts/${id}`)
      .then(res => commit('DELETE_DATA', res.data));
  }
}
