<script setup>
import { computed } from "vue"

const props = defineProps({
  title: String,
  value: [Number, String],
  subtitle: String,
  icon: Object,
  color: {
    type: String,
    default: 'blue'
  }
})

const colorClasses = computed(() => {
  const colors = {
    blue: {
      bg: 'bg-blue-500',
      text: 'text-blue-600',
      iconBg: 'bg-blue-100'
    },
    green: {
      bg: 'bg-green-500',
      text: 'text-green-600',
      iconBg: 'bg-green-100'
    },
    purple: {
      bg: 'bg-purple-500',
      text: 'text-purple-600',
      iconBg: 'bg-purple-100'
    },
    yellow: {
      bg: 'bg-yellow-500',
      text: 'text-yellow-600',
      iconBg: 'bg-yellow-100'
    },
    red: {
      bg: 'bg-red-500',
      text: 'text-red-600',
      iconBg: 'bg-red-100'
    }
  }
  return colors[props.color] || colors.blue
})
</script>

<template>
  <div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="p-5">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div :class="[colorClasses.iconBg, 'flex items-center justify-center h-12 w-12 rounded-md']">
            <component :is="icon" :class="[colorClasses.text, 'h-6 w-6']" />
          </div>
        </div>
        <div class="ml-5 w-0 flex-1">
          <dl>
            <dt class="text-sm font-medium text-gray-500 truncate">{{ title }}</dt>
            <dd class="flex items-baseline">
              <div class="text-2xl font-semibold text-gray-900">
                {{ typeof value === 'number' ? value.toLocaleString() : value }}
              </div>
            </dd>
            <dd v-if="subtitle" class="text-sm text-gray-500">{{ subtitle }}</dd>
          </dl>
        </div>
      </div>
    </div>
  </div>
</template>