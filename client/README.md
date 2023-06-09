# e-commerce

### System requirements

> `Node: v18.16.0 greater than v16.0.0 will work fine.` <br/> `Typescript: 5` <br/> `Vue: 3` <br/>

### System:

`OS: Windows 10 10.0.19045`<br/>
`CPU: (4) x64 Intel(R) Core(TM) i5-3470 CPU @ 3.20GHz`
`Memory: 868.88 MB / 6.94 GB`

### Binaries:

`Node: 18.16.0 - C:\Program Files\nodejs\node.EXE` <br/>
`Yarn: 1.22.19 - ~\AppData\Roaming\npm\yarn.CMD` <br/>
`npm: 9.5.1 - C:\Program Files\nodejs\npm.CMD` <br/>

### Browsers:

`Chrome: 113.0.5672.127` <br/>
`Edge: Spartan (44.19041.1266.0), Chromium (113.0.1774.57)` <br/>
`Internet Explorer: 11.0.19041.1566` <br/>

## API

The `products.api.ts` file related to all the api calls of `products`.

```ts
// src/api/products.api.ts
const Products: {
  list(): Promise<TProduct[]>
  add(product: any): Promise<AxiosResponse<any, any>>
  remove(product_id: Id): Promise<AxiosResponse<any, any>>
}
```

Similarly `attributes.api.ts` is related to fetch attributes api.

```ts
const Attributes: {
  list(product_type_key: string): Promise<TAttribute[]>
}
```

## Project Setup

```sh
npm install
```

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Type-Check, Compile and Minify for Production

```sh
npm run build
```

### Lint with [ESLint](https://eslint.org/)

```sh
npm run lint
```

## Recommended IDE Setup

[VSCode](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur) + [TypeScript Vue Plugin (Volar)](https://marketplace.visualstudio.com/items?itemName=Vue.vscode-typescript-vue-plugin).

## Type Support for `.vue` Imports in TS

TypeScript cannot handle type information for `.vue` imports by default, so we replace the `tsc` CLI with `vue-tsc` for type checking. In editors, we need [TypeScript Vue Plugin (Volar)](https://marketplace.visualstudio.com/items?itemName=Vue.vscode-typescript-vue-plugin) to make the TypeScript language service aware of `.vue` types.

If the standalone TypeScript plugin doesn't feel fast enough to you, Volar has also implemented a [Take Over Mode](https://github.com/johnsoncodehk/volar/discussions/471#discussioncomment-1361669) that is more performant. You can enable it by the following steps:

1. Disable the built-in TypeScript Extension
   1. Run `Extensions: Show Built-in Extensions` from VSCode's command palette
   2. Find `TypeScript and JavaScript Language Features`, right click and select `Disable (Workspace)`
2. Reload the VSCode window by running `Developer: Reload Window` from the command palette.

## Customize configuration

See [Vite Configuration Reference](https://vitejs.dev/config/).
