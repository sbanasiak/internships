<script setup>
import { computed } from "vue"

const props = defineProps({
  type: {
    type: String,
    default: 'line'
  },
  data: Array,
  options: Object
})

// Simple line chart implementation
const lineChart = computed(() => {
  if (!props.data || props.data.length === 0) return null
  
  const width = 400
  const height = 200
  const padding = 40
  
  const maxValue = Math.max(...props.data.map(d => d.companies || d.count || 0))
  const minValue = 0
  
  const points = props.data.map((item, index) => {
    const x = padding + (index * (width - 2 * padding)) / (props.data.length - 1)
    const y = height - padding - ((item.companies || item.count || 0) - minValue) * (height - 2 * padding) / (maxValue - minValue)
    return { x, y, value: item.companies || item.count || 0, label: item.month || item.status }
  })
  
  const pathData = points.map((point, index) => 
    `${index === 0 ? 'M' : 'L'} ${point.x} ${point.y}`
  ).join(' ')
  
  return { width, height, points, pathData, maxValue, minValue }
})

// Simple doughnut chart implementation
const doughnutChart = computed(() => {
  if (!props.data || props.data.length === 0) return null
  
  const total = props.data.reduce((sum, item) => sum + (item.count || 0), 0)
  const radius = 80
  const innerRadius = 50
  const centerX = 120
  const centerY = 120
  
  let currentAngle = 0
  const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
  
  const segments = props.data.map((item, index) => {
    const percentage = (item.count || 0) / total
    const angle = percentage * 2 * Math.PI
    
    const startAngle = currentAngle
    const endAngle = currentAngle + angle
    
    const x1 = centerX + Math.cos(startAngle) * radius
    const y1 = centerY + Math.sin(startAngle) * radius
    const x2 = centerX + Math.cos(endAngle) * radius
    const y2 = centerY + Math.sin(endAngle) * radius
    
    const innerX1 = centerX + Math.cos(startAngle) * innerRadius
    const innerY1 = centerY + Math.sin(startAngle) * innerRadius
    const innerX2 = centerX + Math.cos(endAngle) * innerRadius
    const innerY2 = centerY + Math.sin(endAngle) * innerRadius
    
    const largeArcFlag = angle > Math.PI ? 1 : 0
    
    const pathData = [
      `M ${x1} ${y1}`,
      `A ${radius} ${radius} 0 ${largeArcFlag} 1 ${x2} ${y2}`,
      `L ${innerX2} ${innerY2}`,
      `A ${innerRadius} ${innerRadius} 0 ${largeArcFlag} 0 ${innerX1} ${innerY1}`,
      'Z'
    ].join(' ')
    
    currentAngle += angle
    
    return {
      pathData,
      color: colors[index % colors.length],
      label: item.status,
      value: item.count,
      percentage: Math.round(percentage * 100)
    }
  })
  
  return { segments, total }
})
</script>

<template>
  <div class="w-full h-64">
    <!-- Line Chart -->
    <div v-if="type === 'line' && lineChart" class="w-full h-full flex justify-center">
      <svg :width="lineChart.width" :height="lineChart.height" class="border rounded">
        <!-- Grid lines -->
        <defs>
          <pattern id="grid" width="40" height="20" patternUnits="userSpaceOnUse">
            <path d="M 40 0 L 0 0 0 20" fill="none" stroke="#f3f4f6" stroke-width="1"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid)" />
        
        <!-- Chart line -->
        <path 
          :d="lineChart.pathData" 
          fill="none" 
          stroke="#3B82F6" 
          stroke-width="3"
          class="drop-shadow-sm"
        />
        
        <!-- Data points -->
        <g v-for="(point, index) in lineChart.points" :key="index">
          <circle 
            :cx="point.x" 
            :cy="point.y" 
            r="4" 
            fill="#3B82F6" 
            class="drop-shadow-sm"
          />
          <!-- Labels -->
          <text 
            :x="point.x" 
            :y="lineChart.height - 10" 
            text-anchor="middle" 
            class="text-xs fill-gray-600"
          >
            {{ point.label }}
          </text>
        </g>
        
        <!-- Y-axis labels -->
        <text x="10" y="25" class="text-xs fill-gray-600">{{ lineChart.maxValue }}</text>
        <text x="10" y="190" class="text-xs fill-gray-600">0</text>
      </svg>
    </div>
    
    <!-- Doughnut Chart -->
    <div v-else-if="type === 'doughnut' && doughnutChart" class="w-full h-full flex items-center">
      <div class="flex items-center justify-center w-full">
        <svg width="240" height="240" class="mr-8">
          <g v-for="(segment, index) in doughnutChart.segments" :key="index">
            <path 
              :d="segment.pathData" 
              :fill="segment.color"
              class="hover:opacity-80 transition-opacity cursor-pointer"
            />
          </g>
          <!-- Center text -->
          <text x="120" y="115" text-anchor="middle" class="text-sm fill-gray-600">Total</text>
          <text x="120" y="135" text-anchor="middle" class="text-xl font-semibold fill-gray-900">
            {{ doughnutChart.total }}
          </text>
        </svg>
        
        <!-- Legend -->
        <div class="space-y-2">
          <div 
            v-for="(segment, index) in doughnutChart.segments" 
            :key="index"
            class="flex items-center space-x-2"
          >
            <div 
              :style="{ backgroundColor: segment.color }"
              class="w-3 h-3 rounded-full"
            ></div>
            <span class="text-sm text-gray-700">
              {{ segment.label }} ({{ segment.percentage }}%)
            </span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- No data state -->
    <div v-else class="w-full h-full flex items-center justify-center text-gray-500">
      <div class="text-center">
        <div class="text-4xl mb-2">📊</div>
        <div>No data available</div>
      </div>
    </div>
  </div>
</template>