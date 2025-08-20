<script setup>
import { computed } from "vue"
import { 
  BuildingOfficeIcon, 
  CheckCircleIcon, 
  ClockIcon, 
  XCircleIcon 
} from "@heroicons/vue/24/outline"

const props = defineProps({
  activities: {
    type: Array,
    default: () => []
  }
})

const getStatusIcon = (status) => {
  switch (status) {
    case 'verified':
      return CheckCircleIcon
    case 'pending':
      return ClockIcon
    case 'rejected':
      return XCircleIcon
    default:
      return BuildingOfficeIcon
  }
}

const getStatusColor = (status) => {
  switch (status) {
    case 'verified':
      return 'text-green-500'
    case 'pending':
      return 'text-yellow-500'
    case 'rejected':
      return 'text-red-500'
    default:
      return 'text-gray-500'
  }
}

const getStatusBadgeColor = (status) => {
  switch (status) {
    case 'verified':
      return 'bg-green-100 text-green-800'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    case 'rejected':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}
</script>

<template>
  <div class="flow-root">
    <ul v-if="activities.length > 0" role="list" class="-mb-8">
      <li v-for="(activity, index) in activities" :key="activity.id">
        <div class="relative pb-8">
          <span 
            v-if="index !== activities.length - 1" 
            class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" 
            aria-hidden="true"
          />
          <div class="relative flex space-x-3">
            <div>
              <span :class="[getStatusColor(activity.status), 'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white']">
                <component :is="getStatusIcon(activity.status)" class="h-5 w-5" aria-hidden="true" />
              </span>
            </div>
            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
              <div>
                <p class="text-sm text-gray-900">
                  <InertiaLink 
                    :href="route('company-show', activity.id)"
                    class="font-medium hover:text-indigo-600"
                  >
                    {{ activity.name }}
                  </InertiaLink>
                  created by
                  <span class="font-medium">{{ activity.user }}</span>
                </p>
                <div class="mt-1 flex items-center space-x-2">
                  <span 
                    :class="[getStatusBadgeColor(activity.status), 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize']"
                  >
                    {{ activity.status }}
                  </span>
                </div>
              </div>
              <div class="whitespace-nowrap text-right text-sm text-gray-500">
                <time>{{ activity.created_at }}</time>
              </div>
            </div>
          </div>
        </div>
      </li>
    </ul>
    
    <!-- Empty state -->
    <div v-else class="text-center py-8">
      <BuildingOfficeIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">No recent activity</h3>
      <p class="mt-1 text-sm text-gray-500">
        No companies have been added recently.
      </p>
      <div class="mt-6">
        <InertiaLink
          :href="route('company-create')"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <BuildingOfficeIcon class="-ml-1 mr-2 h-5 w-5" />
          Add company
        </InertiaLink>
      </div>
    </div>
  </div>
</template>