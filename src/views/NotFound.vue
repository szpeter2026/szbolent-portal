<template>
  <div class="not-found-page">
    <div class="container">
      <div class="not-found-content" data-aos="fade-up">
        <h1 class="error-code">404</h1>
        <h2>页面未找到</h2>
        <p>抱歉，您访问的页面不存在或已被移除</p>
        <router-link to="/" class="btn btn-primary btn-large">返回首页</router-link>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.not-found-page {
  min-height: calc(100vh - 70px);
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bolent-gradient);

  .not-found-content {
    text-align: center;
    color: white;

    .error-code {
      font-size: 8rem;
      font-weight: bold;
      margin-bottom: 24px;
      color: white;
      text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    h2 {
      font-size: 2rem;
      margin-bottom: 16px;
      color: white;
    }

    p {
      font-size: 1.125rem;
      margin-bottom: 40px;
      opacity: 0.9;
    }

    .btn {
      background: white;
      color: var(--primary-color);

      &:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
      }
    }
  }
}

@media (max-width: 768px) {
  .not-found-page {
    .not-found-content {
      .error-code {
        font-size: 5rem;
      }

      h2 {
        font-size: 1.5rem;
      }
    }
  }
}
</style>
