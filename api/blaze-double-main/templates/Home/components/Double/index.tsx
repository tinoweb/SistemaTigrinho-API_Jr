import { items } from "@/constants";
import { Ball } from "../Ball";
import { useDouble } from "../../hooks/useDouble";

export const Double = () => {
  const { areaRef, translateX, history, time, isRunning } = useDouble();

  return (
    <div className="flex items-center flex-col gap-2 w-full md:w-1/2 md:h-full justify-center">
      <div className="flex gap-2">
        {history?.map((item) => (
          <Ball
            size={30}
            color={
              item == 0
                ? "bg-white"
                : item % 2 === 0
                ? "bg-black"
                : "bg-red-700"
            }
            textColor={item == 0 ? "text-black" : "text-white"}
            number={item}
          />
        ))}
      </div>
      <div
        ref={areaRef}
        className="border relative border-gray-700 rounded-md w-full overflow-hidden h-3/4 flex items-center"
      >
        <div
          className={`transition-all border border-gray-700 pl-4 duration-[4000ms] ease-in-out`}
          style={{
            transform: `translateX(${translateX}px)`,
          }}
        >
          <div className="gap-3 py-5 flex ">
            {items?.map((item) => (
              <Ball
                size={70}
                number={item.number}
                color={item.color}
                textColor={item.textColor}
              />
            ))}
          </div>
        </div>

        <div
          className={`w-full h-full top-0 flex items-center justify-center absolute ${
            isRunning && "bg-black bg-opacity-60"
          }`}
        >
          {isRunning ? (
            <div className="flex flex-col items-center justify-center">
              <p className="text-white font-bold text-2xl">{time}</p>
            </div>
          ) : (
            <div className="h-full w-1 bg-opacity-40 rounded-md bg-white absolute top-0"></div>
          )}
        </div>
      </div>
    </div>
  );
};
