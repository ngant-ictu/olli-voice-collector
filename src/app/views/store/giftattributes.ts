export const state = () => ({

});

export const mutations = {

};

export const actions = {
  async delete({ commit }, { id }) {
    return await this.$axios
      .$delete(`/v1/giftattributes/${id}`)
      .then(res => commit("DELETE_DATA", res.data));
  },

  async update_field({ commit }, formData) {
    await this.$axios.$put(`/v1/giftattributes/${formData.id}/field`, formData);
  }
};
