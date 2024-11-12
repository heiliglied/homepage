# laravel react 사용하기.(use by vite)  
  
## 관련 프로그램 설치.  
 - npm install react@latest react-dom@latest //기본 리액트 설치  
 - npm install vite //vite 설치  
 - npm install @vitejs/plugin-react //vite react 플러그인 설치  
 - npm install @vitejs/plugin-vue //vite vue 플러그인 설치
  
 ※ 라라벨 관련 추가 프로그램.
 - npm install laravel-vite-plugin //laravel vite 플러그인  
  
## vite.config.js 설정(react).  
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ], //이 배열이 실제 @vite로 view파일에서 읽어오는 부분.
            refresh: true,
        }),
		react(),
    ],
});
```  
  
## vite.config.js 설정(vue).  
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ], //이 배열이 실제 @vite로 view파일에서 읽어오는 부분.
            refresh: true,
        }),
		vue(),
    ],
});
```  
  
## 패키징.
npm run build 명령으로 public 폴더 아래로 컴파일 후 배치함.