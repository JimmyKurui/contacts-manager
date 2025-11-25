import { debounce } from 'lodash';

const DEFAULT_DEBOUNCE = 1000;

export function useDebounce(fn, wait = DEFAULT_DEBOUNCE, options = {}) {
  return debounce(fn, wait, options);
}
