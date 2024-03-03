function generateColorPairs(n)
{
      const colorPairs = {
            backgroundColor: [],
            borderColor: []
      };

      for (let i = 0; i < n; i++)
      {
            const hue = Math.floor(360 * i / n);
            const backgroundColor = `hsla(${ hue }, 100%, 85%, 0.2)`;
            const borderColor = `hsl(${ hue }, 100%, 50%)`;

            colorPairs.backgroundColor.push(backgroundColor);
            colorPairs.borderColor.push(borderColor);
      }

      return colorPairs;
}