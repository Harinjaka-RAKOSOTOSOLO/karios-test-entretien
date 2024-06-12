<template>
  <div>
    <div class="mb-2">
      <h1 class="text-4xl font-bold">Bienvenue sur ma belle application</h1>
      <p class="text-xl">Listing des demandes cliniques</p>
    </div>
    <div class="flex gap-2 flex-col w-full">
      <div
          v-for="depot in depots"
          :key="depot.id"
          class="bg-white rounded-xl shadow-sm p-4"
      >
        <p class="text-base font-semibold">Titre: <span class="text-base text-gray-700 font-light">{{
            depot.titre
          }}</span></p>
        <p class="text-base font-semibold">Description: <span
            class="text-base text-gray-700 font-light">{{ depot.description }}</span></p>
        <p class="text-base font-semibold">Date de création: <span
            class="text-base text-gray-700 font-light">{{ depot.date_creation }}</span></p>
        <div class="my-4 p-2 border border-gray rounded-xl bg-gray-100 flex flex-col gap-2"
             v-if="depot.reponses.length">
          <div class="border border-dashed border-2 bg-white px-4 py-2" v-for="reponse in depot.reponses"
               :key="reponse.id">
            <div>
              <input type="checkbox" v-model="reponse.isValidate"
                     class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
            </div>
            <p class="text-base font-semibold text-red-500">Type: <span
                class="text-base text-gray-700 font-light">{{ getTypeLabel(reponse.type) }}</span></p>
            <p class="text-base font-semibold">Titre: <span class="text-base text-gray-700 font-light">{{
                reponse.titre
              }}</span></p>
            <p class="text-base font-semibold">Description: <span
                class="text-base text-gray-700 font-light">{{ reponse.description }}</span></p>
            <p class="text-base font-semibold">Date de création: <span
                class="text-base text-gray-700 font-light">{{ reponse.date_creation }}</span></p>
          </div>
        </div>
        <div class="flex items-center justify-center" v-else>
          <p class="text-base font-semibold">Aucune réponse</p>
        </div>
        <div class="flex">
          <div class="mb-6" style="display: none" :id="`reasonBlock${depot.id}`">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900">Quelle est la raison de la
              validation ?</label>
            <input
                :ref="`reasonInput${depot.id}`"
                type="text"
                class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
          </div>
        </div>
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2"
                @click="$router.push(`/depots/${depot.id}`)">Répondre à la demande
        </button>
        <button class="bg-green-500 disabled:bg-gray-200 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-2"
                :disabled="!canSubmitValidation(depot)"
                v-if="hasResponses(depot)"
                @click="processValidation(depot)"
        >
          Envoyer la réponse
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import {getLabel} from '@/enum/demande_clinique/reponse/type';
import api from '@/api';

export default {
  name: 'Index',
  computed: {
    ...mapGetters({
      depots: 'demande_clinique/filteredDepot',
    }),
  },
  methods: {
    ...mapActions({
      chargerDepots: 'demande_clinique/chargerDepots',
    }),
    getTypeLabel: getLabel,
    hasResponses(depot) {
      return depot.reponses.length > 0
    },
    canSubmitValidation(depot) {
      return depot.reponses.some((response) => response.isValidate)
    },
    processValidation: async function(depot) {
      const reasonBlock = document.querySelector(`#reasonBlock${depot.id}`)
      if (reasonBlock.style.display !== "block") {
        reasonBlock.style.display = "block"
      } else {
        const reasonInputElement = this.$refs[`reasonInput${depot.id}`]
        const reasonInputValue = reasonInputElement[0]?.value

        if (reasonInputValue) {
          const responseIdsToValidate = depot.reponses
              .filter( response => response.isValidate === true)
              .map( reponse => reponse.id)

          try {
            await api.demande_clinique.reponses.valider(responseIdsToValidate, reasonInputValue);
            await this.chargerDepots();
            reasonBlock.style.display = "none"
            this.$forceUpdate();  // Notice we have to use a $ here

          } catch (e) {
            console.log(e.message)
            window.alert('Une erreur est survenue');
          }
        } else {
          alert('Veuillez renseigner le motif de la validation !')
        }
      }
    }
  }
};
</script>