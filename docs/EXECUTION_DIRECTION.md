# 执行方向共识

> **真源文档：** [`PlanetX/docs/EXECUTION_DIRECTION.md`](../../PlanetX/docs/EXECUTION_DIRECTION.md)  
> **M0 完成：** 2026-07-17  

## 已完成

| 任务 | 产物 |
|------|------|
| JobFirst 收件箱闭环 | `PlanetX/scripts/demo-jobfirst-inbox.sh` |
| 事件契约 | `PlanetX/docs/EVENT_SCHEMA.md` |
| 海外 PWA（代码） | `PlanetX/public/manifest.json`、`sw.js`、`vercel.json`、图标 |

## 待手动（海外上线）

1. Vercel 项目 → Root `PlanetX` → 输出 `.`  
2. DNS `app.genz.ltd` → Vercel  
3. `deploy-overseas.yml` CORS 加 `https://app.genz.ltd`  

## 下一轮

YeDall anchor 接入 demo 脚本 · 录屏 · 3–5 人封闭试用

完整细节见 PlanetX 真源文档 §9。
