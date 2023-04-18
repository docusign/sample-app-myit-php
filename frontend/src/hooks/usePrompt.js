/**
 * Prompts a user when they exit the page
 */

import { useCallback, useContext, useEffect } from "react";
import { UNSAFE_NavigationContext as NavigationContext } from "react-router-dom";

function useConfirmExit(confirmExit, when = true) {
  const { navigator } = useContext(NavigationContext);

  useEffect(() => {
    if (!when) {
      return;
    }

    const { push } = navigator;

    navigator.push = (...args) => {
      const result = confirmExit();
      if (result !== false) {
        push(...args);
      }
    };

    // eslint-disable-next-line consistent-return
    return () => {
      navigator.push = push;
    };
  }, [navigator, confirmExit, when]);
}

export function usePrompt(message, onConfirm, when = true) {
  const confirmExit = useCallback(() => {
    // eslint-disable-next-line no-alert
    const confirm = window.confirm(message);
    if (confirm) {
      onConfirm();
    }
    return confirm;
  }, [message]);
  useConfirmExit(confirmExit, when);
}
