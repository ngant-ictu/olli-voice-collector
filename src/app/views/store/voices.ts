export const state = () => ({
  data: [],
  query: {},
  formSource: {},
  totalItems: 0,
  recordPerPage: 0
});

export const mutations = {
  SET_DATA(state, response) {
    state.data = response.data || null;
    state.totalItems =
      typeof response.meta !== "undefined" ? response.meta.totalItems : 0;
    state.recordPerPage =
      typeof response.meta !== "undefined" ? response.meta.recordPerPage : 0;
  },

  SET_QUERY(state, response) {
    state.query.orderby =
      typeof response.meta !== "undefined"
        ? response.meta.orderBy.toLowerCase()
        : "uid";
    state.query.ordertype =
      typeof response.meta !== "undefined"
        ? response.meta.orderType.toLowerCase()
        : "desc";
    state.query.page =
      typeof response.meta !== "undefined" ? response.meta.page : 1;
  },

  SET_FORM_SOURCE(state, response) {
    state.formSource = response.data || null;
  },

  UPDATE_DATA(state, { editedItem }) {
    const index = state.data.findIndex(item => item.uid === editedItem.uid);
    state.data.splice(index, 1, editedItem);
  }
};

export const actions = {
  async get_all({ commit }, { query }) {
    return await this.$axios
      .$get(`/v1/records`, {
        params: query
      })
      .then(res => {
        commit("SET_DATA", res);
        commit("SET_QUERY", res);
      });
  },

  async get_form_source({ commit }) {
    return await this.$axios
      .$get(`/v1/records/formsource`)
      .then(res => commit("SET_FORM_SOURCE", res));
  },

  async get({ commit }, { id }) {
    return await this.$axios.$get(`/v1/records/${id}`);
  },

  async update({ commit }, { id, formData }) {
    return await this.$axios
      .$put(`/v1/records/${id}`, formData)
      .then(res => commit("UPDATE_DATA", res.data));
  },

  async validate({ commit }, { id, formData }) {
    const res = await this.$axios.$put(`/v1/records/validate/${id}`, formData);
    commit("UPDATE_DATA", {
      editedItem: res.data
    });
  }
};
