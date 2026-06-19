<template>
  <div class="login-page">
    <div class="login-card">
      <h1 class="login-title"> Вход в систему</h1>
      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="email">Email</label>
          <input
            id="email"
            type="email"
            v-model="email"
            placeholder="example@mail.ru"
            required
          />
        </div>
        <div class="form-group">
          <label for="password">Пароль</label>
          <input
            id="password"
            type="password"
            v-model="password"
            placeholder="••••••••"
            required
          />
        </div>
        <button type="submit" class="login-btn" :disabled="loading">
          {{ loading ? 'Вход...' : 'Войти' }}
        </button>
        <div v-if="error" class="error-message">{{ error }}</div>
      </form>
      <p class="login-hint">Для входа используйте: test@example.com / password</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const email = ref('test@example.com')
const password = ref('password')
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  try {
    await authStore.login(email.value, password.value)
    await authStore.fetchUser()
    router.push('/')
  } catch (err) {
    error.value = err || 'Не удалось войти. Проверьте данные.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 70vh;
}
.login-card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.08);
  padding: 2.5rem 2.8rem;
  max-width: 420px;
  width: 100%;
  transition: box-shadow 0.2s;
}
.login-card:hover {
  box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}
.login-title {
  font-weight: 600;
  font-size: 1.8rem;
  margin-bottom: 1.8rem;
  text-align: center;
  color: #1a2634;
}
.login-form .form-group {
  margin-bottom: 1.2rem;
}
.login-form label {
  display: block;
  font-weight: 500;
  font-size: 0.9rem;
  margin-bottom: 0.3rem;
  color: #4a5a6e;
}
.login-form input {
  width: 100%;
  padding: 0.7rem 0.9rem;
  border: 1px solid #d0d7e2;
  border-radius: 8px;
  font-size: 1rem;
  transition: border 0.2s, box-shadow 0.2s;
}
.login-form input:focus {
  outline: none;
  border-color: #4a8cf7;
  box-shadow: 0 0 0 3px rgba(74,140,247,0.15);
}
.login-btn {
  width: 100%;
  padding: 0.75rem;
  background: #4a8cf7;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
  margin-top: 0.5rem;
}
.login-btn:hover:not(:disabled) {
  background: #3b7de0;
}
.login-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.error-message {
  color: #d32f2f;
  font-size: 0.9rem;
  margin-top: 1rem;
  text-align: center;
}
.login-hint {
  margin-top: 1.5rem;
  font-size: 0.85rem;
  color: #7a8a9e;
  text-align: center;
  border-top: 1px solid #edf2f7;
  padding-top: 1.2rem;
}
</style>