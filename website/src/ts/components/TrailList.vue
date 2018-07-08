<template>
  <div>
    <div v-if="sortedTrailers === null" >
        <pre>Could not load data from steemauto</pre>
    </div>
    <div v-else>
      <table id="result" width="400px">
        <tr><td>Steem ID</td><td>&nbsp;&nbsp;&nbsp;</td><td>Weight</td><td>&nbsp;&nbsp;&nbsp;</td><td>Estimated Vote Value</td></tr>
        <tr v-for="trailer in sortedTrailers"><td><a :href="trailer.authorlink">{{ trailer.follower }}</a></td><td>&nbsp;&nbsp;&nbsp;</td><td>{{ trailer.weight }}</td><td>&nbsp;&nbsp;&nbsp;</td><td>{{ trailer.value }} SBD</td></tr>
      </table>
    </div>
  </div>
</template>

<script lang="ts">
  import axios from 'axios';
  import Vue from "vue";
  import {getVoteValue} from "../utils/utils";

  export default Vue.extend({
    data() {
      return {
        sortedTrailers: [{},{}],
        trailers: [],
        accounts: []
      }
    },
    created: async function() {
      var accounts:Array<string> = [];
      let weights:Array<string> = [];
      var name:string;

      await axios.get(`https://steemauto.com/api.php?i=1&user=steemmakers`)
        .then(response => {
        // JSON responses are automatically parsed.
        this.trailers = response.data
      });

      this.trailers.forEach(function(element) {
        name = element['follower'];
        element['weight'] = element['weight'] / 100;
        accounts.push(name);
        weights.push(element['weight']);
      });
      this.sortedTrailers = await getVoteValue(accounts, weights);

      // add author link
      this.sortedTrailers.forEach(function(element) {
        element['authorlink'] = 'https://www.steemit.com/@' + element['follower'];
      });
    }
  });
</script>


<style scoped>

</style>
