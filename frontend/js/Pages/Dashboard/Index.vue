<script setup>
import { ref, onMounted } from "vue"
import { useI18n } from "vue-i18n"
import StatsCard from "./Components/StatsCard.vue"
import ChartComponent from "./Components/ChartComponent.vue"
import RecentActivity from "./Components/RecentActivity.vue"
import { PlusIcon, ChartBarIcon, BuildingOfficeIcon, UserGroupIcon } from "@heroicons/vue/24/outline"

const i18n = useI18n()

const stats = ref({})
const chartData = ref({})
const recentActivity = ref([])
const loading = ref(true)

const fetchDashboardData = async () => {
  try {
    loading.value = true
    
    // Fetch all dashboard data using axios with CSRF protection
    const [statsResponse, chartResponse, activityResponse] = await Promise.all([
      window.axios.get("/api/dashboard/stats"),
      window.axios.get("/api/dashboard/chart-data"),
      window.axios.get("/api/dashboard/recent-activity")
    ])

    stats.value = statsResponse.data
    chartData.value = chartResponse.data
    recentActivity.value = activityResponse.data
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            {{ i18n.t('dashboard.title', 'Analytics Dashboard') }}
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            {{ i18n.t('dashboard.subtitle', 'Overview of internship program statistics and activity') }}
          </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
          <InertiaLink
            :href="route('company-create')"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
            {{ i18n.t('company.add_new', 'Add Company') }}
          </InertiaLink>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>

      <!-- Dashboard Content -->
      <div v-else class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
          <StatsCard
            :title="i18n.t('dashboard.total_companies', 'Total Companies')"
            :value="stats.totalCompanies || 0"
            :icon="BuildingOfficeIcon"
            color="blue"
            :subtitle="i18n.t('dashboard.companies_this_month', '{count} this month', { count: stats.companiesThisMonth || 0 })"
          />
          <StatsCard
            :title="i18n.t('dashboard.total_users', 'Total Users')"
            :value="stats.totalUsers || 0"
            :icon="UserGroupIcon"
            color="green"
            :subtitle="i18n.t('dashboard.registered_users', 'Registered users')"
          />
          <StatsCard
            :title="i18n.t('dashboard.verified_companies', 'Verified Companies')"
            :value="stats.verifiedCompanies || 0"
            :icon="ChartBarIcon"
            color="purple"
            :subtitle="i18n.t('dashboard.pending_review', '{count} pending', { count: stats.pendingCompanies || 0 })"
          />
          <StatsCard
            :title="i18n.t('dashboard.recent_activity', 'Recent Activity')"
            :value="stats.recentCompanies || 0"
            :icon="ChartBarIcon"
            color="yellow"
            :subtitle="i18n.t('dashboard.last_7_days', 'Last 7 days')"
          />
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <!-- Monthly Growth Chart -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                {{ i18n.t('dashboard.monthly_growth', 'Monthly Company Growth') }}
              </h3>
              <ChartComponent
                v-if="chartData.monthlyGrowth"
                type="line"
                :data="chartData.monthlyGrowth"
                :options="{ responsive: true, maintainAspectRatio: false }"
              />
            </div>
          </div>

          <!-- Status Distribution Chart -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                {{ i18n.t('dashboard.status_distribution', 'Company Status Distribution') }}
              </h3>
              <ChartComponent
                v-if="chartData.statusDistribution"
                type="doughnut"
                :data="chartData.statusDistribution"
                :options="{ responsive: true, maintainAspectRatio: false }"
              />
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              {{ i18n.t('dashboard.recent_companies', 'Recently Added Companies') }}
            </h3>
            <RecentActivity :activities="recentActivity" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>