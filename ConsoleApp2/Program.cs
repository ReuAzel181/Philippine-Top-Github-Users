Console.Write("Please input your grade: ");
int grade = 0;
Boolean InvalidInput = false;
try
{
    grade = Convert.ToInt32(Console.ReadLine()); 

}
catch (FormatException)
{
    Console.WriteLine("Invalid Input");
    InvalidInput = true;
}
if (!InvalidInput) { 
    if (grade > 100)
    {
        Console.WriteLine("Invalid Output!");
    }
    else if (grade >= 98)
    {
        Console.WriteLine("You are with highest honor!");
    }
    else if (grade >= 95)
    {
        Console.WriteLine("You are with high honor!");
    }
    else if (grade >= 90)
    {
        Console.WriteLine("You are with honor!");
    }
    else if (grade >= 75)
    {
        Console.WriteLine("You Passed!");
    }
    else
    {
        Console.WriteLine("You Failed!");
    }

}