import { items } from "@/constants";
import { getTranslateByAreaAndValue } from "@/functions";
import { useEffect, useRef, useState } from "react";

export const useArea = (current: number) => {
  const [translateX, setTranslateX] = useState<number>(0);
  const areaRef = useRef<HTMLDivElement>(null);

  const handleMove = (
    randomNumber?: number | string,
    randomMiddle?: boolean
  ) => {
    const translate = getTranslateByAreaAndValue(
      areaRef,
      randomNumber || 0,
      randomMiddle
    );
    setTranslateX(translate);
  };

  useEffect(() => {
    window.addEventListener("resize", () => handleMove(current));
    return () =>
      window.removeEventListener("resize", () => handleMove(current));
  }, [current]);

  return {
    areaRef,
    translateX,
    handleMove,
  };
};
