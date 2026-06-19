<template>
  <div class="reviews-container">
    <div class="reviews-header">
      <h3 class="reviews-title">📝 Отзывы ({{ total }})</h3>
    </div>

    <div v-if="loading" class="loading-spinner">Загрузка отзывов...</div>

    <div v-else>
      <div v-if="reviews.length === 0" class="empty-state">
        Отзывов пока нет.
      </div>
      <div v-else>
        <div v-for="review in reviews" :key="review.id || review.date" class="review-card">
          <div class="review-meta">
            <span class="review-author">{{ review.author }}</span>
            <span class="review-date">{{ review.date }}</span>
            <span class="review-rating">⭐ {{ review.rating }}</span>
          </div>
          <p class="review-text">{{ review.text }}</p>
        </div>

        <div class="pagination-bar">
          <button
            @click="changePage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="page-btn"
          >
            ← Назад
          </button>
          <span class="page-info">Страница {{ currentPage }} из {{ lastPage }}</span>
          <button
            @click="changePage(currentPage + 1)"
            :disabled="currentPage === lastPage"
            class="page-btn"
          >
            Вперед →
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, defineProps } from 'vue'
import api from '@/api/axios'

const props = defineProps({
  organizationId: {
    type: Number,
    required: true,
  },
})

const reviews = ref([])
const currentPage = ref(1)
const perPage = ref(50)
const total = ref(0)
const lastPage = ref(1)
const loading = ref(false)

const loadReviews = async (page) => {
  loading.value = true
  try {
    const response = await api.get(`/organizations/${props.organizationId}/reviews`, {
      params: { page, per_page: perPage.value },
    })
    reviews.value = response.data.data
    total.value = response.data.total
    currentPage.value = response.data.current_page
    lastPage.value = response.data.last_page
  } catch (error) {
    console.error('Ошибка загрузки отзывов:', error)
  } finally {
    loading.value = false
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= lastPage.value) {
    loadReviews(page)
  }
}

watch(() => props.organizationId, () => {
  if (props.organizationId) loadReviews(1)
}, { immediate: true })
</script>

<style scoped>
.reviews-container {
  margin-top: 0.5rem;
}
.reviews-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.2rem;
}
.reviews-title {
  font-weight: 600;
  font-size: 1.2rem;
  color: #1a2634;
  margin: 0;
}
.loading-spinner {
  text-align: center;
  color: #7a8a9e;
  padding: 2rem 0;
}
.empty-state {
  text-align: center;
  color: #7a8a9e;
  padding: 2rem 0;
  font-style: italic;
}
.review-card {
  background: #f8faff;
  border-radius: 10px;
  padding: 1rem 1.2rem;
  margin-bottom: 0.8rem;
  border-left: 4px solid #4a8cf7;
  transition: background 0.2s;
}
.review-card:hover {
  background: #f0f6ff;
}
.review-meta {
  display: flex;
  gap: 1rem;
  align-items: center;
  flex-wrap: wrap;
  margin-bottom: 0.4rem;
}
.review-author {
  font-weight: 600;
  color: #1a2634;
}
.review-date {
  font-size: 0.85rem;
  color: #7a8a9e;
}
.review-rating {
  margin-left: auto;
  background: #eef4fa;
  padding: 0.1rem 0.6rem;
  border-radius: 12px;
  font-size: 0.9rem;
}
.review-text {
  margin: 0.3rem 0 0 0;
  line-height: 1.5;
  color: #2c3e50;
}
.pagination-bar {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  margin-top: 1.8rem;
  padding-top: 1rem;
  border-top: 1px solid #eef2f7;
}
.page-btn {
  padding: 0.4rem 1.2rem;
  background: #eef2f7;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  color: #2c3e50;
  cursor: pointer;
  transition: background 0.2s;
}
.page-btn:hover:not(:disabled) {
  background: #e2e8f0;
}
.page-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.page-info {
  font-size: 0.95rem;
  color: #4a5a6e;
}
</style>