<template>
  <header class="header" :class="{ 'header-scrolled': isScrolled }">
    <div class="container">
      <div class="header-wrapper">
        <!-- Logo -->
        <router-link to="/" class="logo">
          <span class="logo-icon">🚀</span>
          <span class="logo-text">Bolent</span>
        </router-link>

        <!-- 桌面导航 -->
        <nav class="nav-desktop">
          <ul class="nav-menu">
            <li>
              <router-link to="/" class="nav-link">首页</router-link>
            </li>
            <li>
              <router-link to="/about" class="nav-link">关于我们</router-link>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link">
                服务
                <i class="icon-arrow-down"></i>
              </a>
              <ul class="dropdown-menu">
                <li><router-link to="/services/outsourcing">IT 外包</router-link></li>
                <li><router-link to="/services/agile">敏捷咨询</router-link></li>
                <li><router-link to="/services/automation">自动化 & QA</router-link></li>
                <li><router-link to="/services/development">软件开发</router-link></li>
                <li><router-link to="/services/digital">数字化 & 数据</router-link></li>
                <li><router-link to="/services/it-management">IT 管理</router-link></li>
              </ul>
            </li>
            <li>
              <router-link to="/blog" class="nav-link">博客</router-link>
            </li>
            <li>
              <router-link to="/case-study" class="nav-link">案例研究</router-link>
            </li>
            <li>
              <router-link to="/careers" class="nav-link">加入我们</router-link>
            </li>
          </ul>
        </nav>

        <!-- CTA 按钮 -->
        <router-link to="/contact" class="btn btn-contact">联系我们</router-link>

        <!-- 移动端菜单按钮 -->
        <button class="mobile-menu-toggle" @click="toggleMobileMenu">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </div>

    <!-- 移动端导航 -->
    <div class="mobile-nav" :class="{ 'mobile-nav-open': isMobileMenuOpen }">
      <nav>
        <ul class="mobile-nav-menu">
          <li><router-link to="/" @click="closeMobileMenu">首页</router-link></li>
          <li><router-link to="/about" @click="closeMobileMenu">关于我们</router-link></li>
          <li>
            <a href="#" @click.prevent="toggleServicesMenu">
              服务
              <i class="icon-arrow-down" :class="{ 'rotate': isServicesOpen }"></i>
            </a>
            <ul class="submenu" v-show="isServicesOpen">
              <li><router-link to="/services/outsourcing" @click="closeMobileMenu">IT 外包</router-link></li>
              <li><router-link to="/services/agile" @click="closeMobileMenu">敏捷咨询</router-link></li>
              <li><router-link to="/services/automation" @click="closeMobileMenu">自动化 & QA</router-link></li>
              <li><router-link to="/services/development" @click="closeMobileMenu">软件开发</router-link></li>
              <li><router-link to="/services/digital" @click="closeMobileMenu">数字化 & 数据</router-link></li>
              <li><router-link to="/services/it-management" @click="closeMobileMenu">IT 管理</router-link></li>
            </ul>
          </li>
          <li><router-link to="/blog" @click="closeMobileMenu">博客</router-link></li>
          <li><router-link to="/case-study" @click="closeMobileMenu">案例研究</router-link></li>
          <li><router-link to="/careers" @click="closeMobileMenu">加入我们</router-link></li>
          <li><router-link to="/contact" @click="closeMobileMenu" class="btn btn-primary">联系我们</router-link></li>
        </ul>
      </nav>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const isScrolled = ref(false)
const isMobileMenuOpen = ref(false)
const isServicesOpen = ref(false)

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50
}

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
  document.body.style.overflow = isMobileMenuOpen.value ? 'hidden' : ''
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
  isServicesOpen.value = false
  document.body.style.overflow = ''
}

const toggleServicesMenu = () => {
  isServicesOpen.value = !isServicesOpen.value
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.body.style.overflow = ''
})
</script>

<style scoped lang="scss">
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;

  &.header-scrolled {
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
  }

  .header-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 70px;
  }

  .logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 28px;
    font-weight: bold;
    text-decoration: none;
    transition: all 0.3s ease;

    &:hover {
      transform: translateY(-2px);
    }

    .logo-icon {
      font-size: 32px;
      filter: drop-shadow(0 2px 4px rgba(24, 144, 255, 0.3));
      transition: all 0.3s ease;
    }

    &:hover .logo-icon {
      transform: rotate(10deg) scale(1.1);
    }

    img {
      height: 40px;
    }

    .logo-text {
      background: linear-gradient(135deg, #1890ff 0%, #096dd9 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-weight: 700;
      letter-spacing: -0.5px;
    }
  }

  .nav-desktop {
    flex: 1;
    display: flex;
    justify-content: center;

    .nav-menu {
      display: flex;
      align-items: center;
      gap: 32px;

      li {
        position: relative;

        &.dropdown:hover .dropdown-menu {
          opacity: 1;
          visibility: visible;
          transform: translateY(0);
        }
      }

      .nav-link {
        font-size: 15px;
        font-weight: 500;
        padding: 8px 0;
        display: flex;
        align-items: center;
        gap: 4px;
        color: var(--text-dark);
        transition: color 0.3s;

        &:hover,
        &.router-link-active {
          color: var(--primary-color);
        }

        .icon-arrow-down::before {
          content: '▼';
          font-size: 10px;
        }
      }

      .dropdown-menu {
        position: absolute;
        top: 100%;
        left: -20px;
        min-width: 200px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 12px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;

        li {
          a {
            display: block;
            padding: 10px 24px;
            color: var(--text-dark);
            font-size: 14px;
            transition: all 0.3s;

            &:hover {
              background: var(--bg-light);
              color: var(--primary-color);
              padding-left: 28px;
            }
          }
        }
      }
    }
  }

  .btn-contact {
    padding: 10px 28px;
    background: var(--primary-color);
    color: white;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s;

    &:hover {
      background: #096dd9;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(24, 144, 255, 0.3);
    }
  }

  .mobile-menu-toggle {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;

    span {
      display: block;
      width: 25px;
      height: 3px;
      background: var(--text-dark);
      border-radius: 2px;
      transition: all 0.3s;
    }
  }

  .mobile-nav {
    display: none;
  }
}

/* 响应式 */
@media (max-width: 992px) {
  .header {
    .nav-desktop,
    .btn-contact {
      display: none;
    }

    .mobile-menu-toggle {
      display: flex;
    }

    .mobile-nav {
      display: block;
      position: fixed;
      top: 70px;
      left: 0;
      right: 0;
      bottom: 0;
      background: white;
      transform: translateX(100%);
      transition: transform 0.3s ease;
      overflow-y: auto;
      padding: 24px;

      &.mobile-nav-open {
        transform: translateX(0);
      }

      .mobile-nav-menu {
        li {
          border-bottom: 1px solid var(--border-color);

          &:last-child {
            border-bottom: none;
          }

          a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 0;
            font-size: 16px;
            color: var(--text-dark);

            &.router-link-active {
              color: var(--primary-color);
            }

            .icon-arrow-down {
              transition: transform 0.3s;

              &::before {
                content: '▼';
                font-size: 10px;
              }

              &.rotate {
                transform: rotate(180deg);
              }
            }
          }

          .submenu {
            padding-left: 20px;

            li {
              border-bottom: none;

              a {
                padding: 12px 0;
                font-size: 14px;
              }
            }
          }

          .btn {
            width: 100%;
            margin-top: 16px;
            justify-content: center;
          }
        }
      }
    }
  }
}
</style>
