<template>
  <div class="dashboard">
    <section class="card">
      <h2 class="card-title">📌 Добавить организацию</h2>
      <form @submit.prevent="saveOrganization" class="add-form">
        <div class="input-group">
          <input
            type="url"
            v-model="url"
            placeholder="https://yandex.ru/maps/org/..."
            required
            class="url-input"
          />
          <button type="submit" class="btn-primary" :disabled="loading">
            {{ loading ? 'Парсим...' : 'Сохранить' }}
          </button>
        </div>
      </form>
    </section>

    <section v-if="organization" class="card stats-card">
      <div class="stats-header">
        <h2 class="org-name">{{ organization.name }}</h2>
        <div class="rating-badge">
          ⭐ {{ organization.average_rating || 0 }} / 5
        </div>
      </div>
      <div class="stats-grid">
        <div class="stat-item">
          <span class="stat-value">{{ organization.total_ratings || 0 }}</span>
          <span class="stat-label">Оценок</span>
        </div>
        <div class="stat-item">
          <span class="stat-value">{{ organization.total_reviews || 0 }}</span>
          <span class="stat-label">Отзывов</span>
        </div>
      </div>
      <div class="actions-row">
        <button @click="refresh" class="btn-secondary">🔄 Обновить</button>
        <button @click="deleteOrg" class="btn-danger">🗑️ Удалить</button>
      </div>
    </section>

    <section v-if="organization" class="card reviews-section">
      <ReviewsList :organization-id="organization.id" />
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/api/axios'
import ReviewsList from './ReviewsList.vue'

const url = ref('')
const organization = ref(null)
const loading = ref(false)

const saveOrganization = async () => {
  loading.value = true
  try {
    const response = await api.post('/organizations', { yandex_url: url.value })
    organization.value = response.data
    url.value = ''
  } catch (error) {
    alert('Ошибка: ' + (error.response?.data?.error || 'Не удалось спарсить'))
  } finally {
    loading.value = false
  }
}

const refresh = async () => {
  if (!organization.value) return
  try {
    const response = await api.post(`/organizations/${organization.value.id}/refresh`)
    organization.value = response.data
  } catch (error) {
    alert('Ошибка обновления')
  }
}

const deleteOrg = async () => {
  if (!organization.value) return
  if (!confirm('Удалить организацию?')) return
  try {
    await api.delete(`/organizations/${organization.value.id}`)
    organization.value = null
  } catch (error) {
    alert('Ошибка удаления')
  }
}
</script>

<style scoped>
.dashboard {
  display: flex;
  flex-direction: column;
  gap: 1.8rem;
}
.card {
  background: #ffffff;
  border-radius: 16px;
  padding: 1.8rem 2rem;
  box-shadow: 0 4px 14px rgba(0,0,0,0.04);
  border: 1px solid #eef2f7;
}
.card-title {
  font-weight: 600;
  font-size: 1.3rem;
  margin-bottom: 1.2rem;
  color: #1a2634;
}
.add-form .input-group {
  display: flex;
  gap: 0.8rem;
  flex-wrap: wrap;
}
.url-input {
  flex: 1;
  padding: 0.7rem 1rem;
  border: 1px solid #d0d7e2;
  border-radius: 8px;
  font-size: 1rem;
  min-width: 200px;
}
.url-input:focus {
  outline: none;
  border-color: #4a8cf7;
  box-shadow: 0 0 0 3px rgba(74,140,247,0.1);
}
.btn-primary {
  padding: 0.7rem 1.8rem;
  background: #4a8cf7;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
  white-space: nowrap;
}
.btn-primary:hover:not(:disabled) {
  background: #3b7de0;
}
.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.stats-card .stats-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  margin-bottom: 1rem;
}
.org-name {
  font-size: 1.6rem;
  font-weight: 600;
  color: #1a2634;
  margin: 0;
}
.rating-badge {
  background: #f0f7ff;
  padding: 0.3rem 1rem;
  border-radius: 20px;
  font-weight: 600;
  color: #2a6bb0;
  font-size: 1.1rem;
}
.stats-grid {
  display: flex;
  gap: 2.5rem;
  margin: 1.2rem 0 1.5rem;
}
.stat-item {
  display: flex;
  flex-direction: column;
}
.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #1a2634;
}
.stat-label {
  font-size: 0.85rem;
  color: #7a8a9e;
  margin-top: 0.1rem;
}
.actions-row {
  display: flex;
  gap: 0.8rem;
  flex-wrap: wrap;
}
.btn-secondary {
  padding: 0.5rem 1.2rem;
  background: #eef2f7;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  color: #2c3e50;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-secondary:hover {
  background: #e2e8f0;
}
.btn-danger {
  padding: 0.5rem 1.2rem;
  background: #fee8e8;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  color: #b33c3c;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-danger:hover {
  background: #fcd5d5;
}
.reviews-section {
  padding-top: 1.5rem;
}
</style>