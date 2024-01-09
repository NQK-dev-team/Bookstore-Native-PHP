function isDobValid(input)
{
      const dob = new Date(input);
      const today = new Date();

      return today >= dob;
}

function isAgeValid(input)
{
      const dob = new Date(input);
      const today = new Date();
      const age = today.getFullYear() - dob.getFullYear();

      // Check if the birthday has occurred this year
      if (today.getMonth() < dob.getMonth() || (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate()))
            age--;

      return age >= 18;
}