<template>
  <div id="app">
    <header v-if="authStore.isAuthenticated" class="app-header">
      <div class="header-content">
        <span class="greeting">Привет {{ authStore.user?.name }}</span>
        <button @click="logout" class="logout-btn">Выйти</button>
      </div>
    </header>
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f4f7fc;
  color: #333;
  min-height: 100vh;
}
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
.app-header {
  background: #ffffff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  padding: 1rem 2rem;
  border-bottom: 1px solid #e9edf2;
}
.header-content {
  max-width: 1000px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.greeting {
  font-weight: 500;
  font-size: 1.1rem;
}
.logout-btn {
  background: none;
  border: 1px solid #d0d7e2;
  border-radius: 6px;
  padding: 0.4rem 1rem;
  font-size: 0.9rem;
  color: #5b6778;
  cursor: pointer;
  transition: all 0.2s;
}
.logout-btn:hover {
  background: #f0f3f7;
  border-color: #b0bccd;
}
.main-content {
  flex: 1;
  padding: 2rem;
  max-width: 1000px;
  margin: 0 auto;
  width: 100%;
}
</style>