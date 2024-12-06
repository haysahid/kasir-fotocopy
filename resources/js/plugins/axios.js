import axios from 'axios';

export default {
  install: (app, options) => {
    app.provide('axios', axios.create({
      baseURL: options.baseUrl ?? null,
    }))
  }
}