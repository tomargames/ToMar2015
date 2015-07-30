import java.io.BufferedReader;
import java.io.FileReader;
import java.util.ArrayList;
import java.util.HashMap;


public class CryptoHelp
{
	static ArrayList<ArrayList<String>> WordLists;
	static HashMap<String,String> alphabetMap = new HashMap<String, String>();
	static HashMap<String,String> alphabetMapTest = new HashMap<String, String>();
	static final String alphabet = "abcdefghijklmnopqrstuvwxyz";
	static String[] wordsIn;
	static String[] wordsOut;
	static int[] inputIndex;
	static int wordIndex;

	public CryptoHelp(String[] words)
	{
		WordLists = new ArrayList<ArrayList<String>>();
		for (int i = 0; i < 25; i++)
		{
			WordLists.add(new ArrayList<String>());
		}	
		//read the word files in
		readFile("words3.txt");
		readFile("words4.txt");
		readFile("words5.txt");
		readFile("words6.txt");
		readFile("wordsRest.txt");
		for (int i = 0; i < 25; i++)
		{
			log("Length: " + (i+3) + " = " + (WordLists.get(i)).size());
		}	
		resetMaps();
		wordsIn = words;
		wordsOut = new String[words.length];
		inputIndex = new int[words.length];
		wordIndex = 0;
		while (true)
		{	
			for (inputIndex[wordIndex] = 0; inputIndex[wordIndex] < (WordLists.get(wordsIn[wordIndex].length() - 3)).size(); inputIndex[wordIndex]++)
			{
				if (tryWord((WordLists.get(wordsIn[wordIndex].length() - 3)).get(inputIndex[wordIndex]), wordsIn[wordIndex]))
				{
					alphabetMap = alphabetMapTest;
					wordIndex+= 1;
					break;
				}
				else
				{
					alphabetMapTest = alphabetMap;
					continue;
				}
			}
		}
		
//		wordListIndex = (words[i].length() < 7) ? words[i].length() - 3 : 4;
//		for (int j = 0; j < (WordLists.get(wordListIndex)).size(); j++)
//		{
//			if (tryWord((String) WordLists.get(listIndex).get(j)))
//			{
//				setWord(i, (String) WordLists.get(listIndex).get(j));
//				wordsOut[i] = (String) WordLists.get(listIndex).get(j);
//				log("Level " + i + " word is " + wordsOut[i]);
//			}
//		}
//	}
}
	private static void resetMaps()
	{
		for (int i = 0; i < 26; i++)
		{
			alphabetMap.put(alphabet.substring(i, i+1), "");
			alphabetMapTest.put(alphabet.substring(i, i+1), "");
		}
	}
	private static boolean tryWord(String word, String codeWord)
	{
		for (int i = 0; i < word.length(); i++)
		{
			String ch1 = word.substring(i, i+1);
			String ch2 = codeWord.substring(i, i+1);
			if (alphabetMapTest.get(ch2).equalsIgnoreCase(ch1)
			|| alphabetMapTest.get(ch2).equalsIgnoreCase(""))
			{
				alphabetMapTest.put(ch2, ch1);
			}
			else
			{
				return false;
			}
		}
		log("Using " + word + " for " + codeWord);
		return true;
	}
	private static void log(String s)
	{
		System.out.println(s);
	}
	public static void readFile(String filename)
	{	
		String s = new String();
        try
        {
			BufferedReader br = new BufferedReader(new FileReader(filename));
            while   (true)
            {
                s = br.readLine();
                if  (s == null)
                {
                    break;
                }
                (WordLists.get(s.length() - 3)).add(s.toLowerCase());
            }
            br.close();
		}	
		catch   (Exception e)
		{
            log("Exception = " + e);
		}
	}
	
	public static void main(String[] args)
	{
		String[] words = { "lgz", "fmlpxdf", "qedzenlmjg", "lvnxldeg"};
		CryptoHelp ch = new CryptoHelp(words);
	}
}
