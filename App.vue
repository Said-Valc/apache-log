<template>
  <div class="p-4">
    <input v-model="search" @input="fetchLogs" placeholder="Search IP or Request" class="mb-2 p-2 border" />

    <table class="w-full border">
      <thead>
        <tr>
          <th @click="sortBy('ip')">IP</th>
          <th @click="sortBy('date')">Date</th>
          <th @click="sortBy('request')">Request</th>
          <th @click="sortBy('status')">Status</th>
          <th @click="sortBy('size')">Size</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(log, index) in logs" :key="index">
          <td>{{ log.ip }}</td>
          <td>{{ log.date }}</td>
          <td>{{ log.request }}</td>
          <td>{{ log.status }}</td>
          <td>{{ log.size }}</td>
        </tr>
      </tbody>
    </table>

    <div class="mt-4 flex gap-2">
      <button @click="prevPage" :disabled="page === 1">Previous</button>
      <span>Page {{ page }}</span>
      <button @click="nextPage" :disabled="page * limit >= total">Next</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const logs = ref([])
const total = ref(0)
const page = ref(1)
const limit = ref(10)
const search = ref('')
const sortKey = ref('')
const sortAsc = ref(true)

const fetchLogs = async () => {
  const res = await axios.get('http://tests/', {
    params: {
      page: page.value,
      limit: limit.value,
      search: search.value
    }
  })
  console.log(res)
  logs.value = res.data.data
  total.value = res.data.total
}

const prevPage = () => {
  if (page.value > 1) {
    page.value--
    fetchLogs()
  }
}

const nextPage = () => {
  if (page.value * limit.value < total.value) {
    page.value++
    fetchLogs()
  }
}

const sortBy = (key) => {
  if (sortKey.value === key) {
    sortAsc.value = !sortAsc.value
  } else {
    sortKey.value = key
    sortAsc.value = true
  }

  logs.value.sort((a, b) => {
    if (a[key] < b[key]) return sortAsc.value ? -1 : 1
    if (a[key] > b[key]) return sortAsc.value ? 1 : -1
    return 0
  })
}

onMounted(fetchLogs)
</script>

<style scoped>
th {
  cursor: pointer;
}
</style>
