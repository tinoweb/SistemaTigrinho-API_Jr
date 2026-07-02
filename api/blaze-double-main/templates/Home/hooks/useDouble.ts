import { useCallback, useEffect, useState } from "react";
import { useArea } from "./useArea";
import { useLocalUserStore } from "@/store/localUserStore";
import { useTimer } from "@/hooks/useTimer";
import { useDoubleStore } from "@/store/doubleStore";
import { generateRandomNumber } from "@/functions";

export const useDouble = () => {
  const [current, setCurrent] = useState<number>(0);
  const [history, setHistory] = useState<number[]>([0]);
  const bet = useDoubleStore((state) => state.bet);
  const setBet = useDoubleStore((state) => state.setBet);
  const selectedColor = useDoubleStore((state) => state.selectedColor);
  const balance = useLocalUserStore((state) => state.balance);
  const increaseToBalance = useLocalUserStore(
    (state) => state.increaseToBalance
  );
  const isBeting = useDoubleStore((state) => state.isBeting);
  const setIsBeting = useDoubleStore((state) => state.setIsBeting);
  const setIsRunning = useDoubleStore((state) => state.setIsRunning);
  const isRunning = useDoubleStore((state) => state.isRunning);
  const { start, reset, time } = useTimer(10, setIsRunning);
  const { areaRef, handleMove, translateX } = useArea(current);

  const handleBet = useCallback(() => {
    if (bet > balance) setBet(balance);
    if (isBeting) increaseToBalance(-bet);
    const { result, color, randomNumber } = generateRandomNumber();

    setCurrent(result);
    handleMove(result, true);

    setTimeout(() => {
      handleMove(result);
    }, 4700);

    setTimeout(() => {
      handleMove("0");
      if (color.includes(selectedColor) && isBeting) {
        const is14x = color.includes("white");
        increaseToBalance(bet * (is14x ? 14 : 2));
      }
      setIsBeting(false);
      setHistory((prev) => [...prev.slice(-10), randomNumber]);
      reset();
      start();
    }, 8000);
  }, [isBeting, bet, selectedColor]);

  useEffect(() => {
    if (time === 0) {
      reset();
      handleBet();
    }
  }, [time]);

  return {
    areaRef,
    history,
    translateX,
    time,
    isRunning,
  };
};
