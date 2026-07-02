import { items } from "@/constants";
import { RefObject } from "react";

export const generateRandomNumber = () => {
  const randomNumber = Math.floor(Math.random() * 30);
  const randomRow = Math.floor(Math.random() * 4);
  const result = randomNumber + 31 * randomRow;
  const color = items[result + 1].color;
  return { result, color, randomNumber };
};

export const getTranslateByAreaAndValue = (
  areaRef: RefObject<HTMLDivElement>,
  current: number | string,
  randomMiddle = false
) => {
  const clientWidth = areaRef.current?.clientWidth;
  const childWidth = areaRef.current?.children[0].clientWidth;
  const distanceBetweenBalls = childWidth! / items.length;
  const distanceToCenter = clientWidth! / 2 - childWidth! / 2;
  const start = 62 * distanceBetweenBalls;
  const translateX = distanceToCenter - distanceBetweenBalls * Number(current);
  const result = translateX + distanceBetweenBalls / 2 - -start - 8;
  if (!randomMiddle) return result;

  const randomPositionInTheMiddle = Math.floor(Math.random() * 30);
  const randomPositiveOrNegative = Math.random() < 0.5 ? -1 : 1;
  const randomTranslateX = randomPositionInTheMiddle * randomPositiveOrNegative;
  return result + randomTranslateX;
};
