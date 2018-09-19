export const state = () => ({
  data: [],
  query: {},
  formSource: {},
  totalItems: 0,
  recordPerPage: 0,
  page: 1
});

export const mutations = {
  SET_DATA(state, response) {
    state.data = response.data || null;
    state.totalItems =
      typeof response.meta !== "undefined" ? response.meta.totalItems : 0;
    state.recordPerPage =
      typeof response.meta !== "undefined" ? response.meta.recordPerPage : 0;
    state.page = typeof response.meta !== "undefined" ? response.meta.page : 0;
  },

  SET_QUERY(state, response) {
    state.query.orderby =
      typeof response.meta !== "undefined"
        ? response.meta.orderBy.toLowerCase()
        : "id";
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

  UPDATE_DATA(state, editedItem) {
    const index = state.data.findIndex(item => item.id === editedItem.id);
    state.data.splice(index, 1, editedItem);
  }
};

export const actions = {
  async get_all({ commit }, { query }) {
    return await this.$axios
      .$get(`/v1/records/user?include=voicescript`, {
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
  }
};
