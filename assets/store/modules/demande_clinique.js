import api from '@/api';

export default {
  namespaced: true,
  state: {
    depots: [],
    filteredDepot: []
  },
  mutations: {
    SET_DEPOTS(state, depots) {
      state.depots = depots;
    },
    SET_FILTERED_DEPOTS(state, depots) {
      state.filteredDepot = depots
    },
  },
  actions: {
    async chargerDepots({commit, state}) {
      commit('SET_DEPOTS', await api.demande_clinique.depots.all());


      const filteredDepot = state.depots.map( (depot) => {
        depot.reponses = depot.reponses.filter((reponse) => !reponse.isValidate)

        return depot
      })
      commit('SET_FILTERED_DEPOTS', filteredDepot);
    },
  },
  getters: {
    depots: state => state.depots,
    filteredDepot: state => state.filteredDepot,
  }
};