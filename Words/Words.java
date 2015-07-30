import java.util.*;
import java.io.*;
/**
 * This class can take a variable number of parameters on the command
 * line. Program execution begins with the main() method. The class
 * constructor is not invoked unless an object of type 'Class1'
 * created in the main() method.
 */
public class Words
{
	public ArrayList<String> readFile(String filename)
	{	
		ArrayList<String> wds = new ArrayList<String>();
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
                wds.add(s.toLowerCase());
            }
            br.close();
		}	
		catch   (Exception e)
		{
            log("Exception = " + e);
		}
		return wds;
	}
	
	public boolean goodWord(String guess, String tgt)
	{
		for (int i = 0; i < guess.length(); i++)
		{
			String g = guess.substring(i, i + 1);
			boolean found = false;
			for (int j = 0; j < tgt.length(); j++)
			{
				if (g.equals(tgt.substring(j, j + 1)))
				{
					tgt = tgt.substring(0,j) + 
					tgt.substring(j+1, tgt.length());
					found = true;
					break;
				}
			}
			if (!found)
			{
				return false;
			}	
		}	
		return true;
	}
	private void wordMania(int min, int max)
	{
		//initialize the word vectors
		ArrayList<String>[] wl = new ArrayList[4];
		//read the word files in
		for (int i = 3; i < 7; i++)
		{	
			wl[i - 3] = readFile("words" + i + ".txt");
		}
		log("Running for minimum of " + min + " and maximum of " + max);
		int[] counters = new int[max];
		for (int i = 0; i < max; i++)
		{
			counters[i] = 0;
		}
		log("Words in: " + wl[3].size());
		int wordsOut = 0;
		int wordsDropped = 0;
		try
		{
			PrintWriter outfile = new PrintWriter(new FileOutputStream("words6wm.txt"), true);
			//initialize ArrayLists
			for (int r = 0; r < wl[3].size(); r++)  //for running through 6-letter words
			{	
				String answerWord = (String) wl[3].get(r);
				int answerCount[] = {0, 0, 0, 0}; //this is for analysis of word distribution
				int idx = 0;
				// set up answer vectors
				for (int a = 0; a < 4; a++)   // run through all 4 word lists
				{	
					answerCount[a] = 0;
					for (int i = 0; i < wl[a].size(); i++)
					{
						String guess = (String) wl[a].get(i);
						if (goodWord(guess, answerWord) == true)
						{
							answerCount[a]++;
							idx++;
							if (idx > max)
								break;
						}	
					}
					if (idx > max)
						break;
				}
				if (idx >= min && idx <= max)
				{
					outfile.write(answerWord + "\r\n");
					wordsOut += 1;
					counters[idx - 1] += 1;
					if (idx > 80)
					{
						log(answerWord);
					}
				}
				else
				{
					wordsDropped += 1;
				}
			}
			log("Words out: " + wordsOut);
			log("Words dropped: " + wordsDropped);
			for (int i = 0; i < max; i++)
			{
				log("Words: " + (i+1) + " = " + counters[i]);
			}
			outfile.close();
		}
		catch (Exception e)
		{	
			log("File open error = " + e.getMessage());
		}	
	}
	private void uwm(int min, int max)
	{
		//initialize the word vectors
		ArrayList<String>[] wl = new ArrayList[4];
		//read the word files in
		for (int i = 3; i < 7; i++)
		{	
			wl[i - 3] = readFile("words" + i + ".txt");
		}
		log("Running for minimum of " + min + " and maximum of " + max);
		int[] counters = new int[max];
		for (int i = 0; i < max; i++)
		{
			counters[i] = 0;
		}
		log("Words in: " + wl[3].size());
		int wordsOut = 0;
		int wordsDropped = 0;
		try
		{
			PrintWriter outfile = new PrintWriter(new FileOutputStream("wordsuwm.txt"), true);
			//initialize ArrayLists
			for (int r = 0; r < wl[3].size(); r++)  //for running through 6-letter words
			{	
				String answerWord = (String) wl[3].get(r);
				int answerCount[] = {0, 0, 0, 0}; //this is for analysis of word distribution
				int idx = 0;
				// set up answer vectors
				for (int a = 0; a < 4; a++)   // run through all 4 word lists
				{	
					answerCount[a] = 0;
					for (int i = 0; i < wl[a].size(); i++)
					{
						String guess = (String) wl[a].get(i);
						if (goodWord(guess, answerWord) == true)
						{
							answerCount[a]++;
							idx++;
							if (idx > max)
								break;
						}	
					}
					if (idx > max)
						break;
				}
				if (idx >= min && idx <= max)
				{
					outfile.write(answerWord + "\r\n");
					wordsOut += 1;
					counters[idx - 1] += 1;
					if (idx > 80)
					{
						log(answerWord);
					}
				}
				else
				{
					wordsDropped += 1;
				}
			}
			log("Words out: " + wordsOut);
			log("Words dropped: " + wordsDropped);
			for (int i = 0; i < max; i++)
			{
				log("Words: " + (i+1) + " = " + counters[i]);
			}
			outfile.close();
		}
		catch (Exception e)
		{	
			log("File open error = " + e.getMessage());
		}	
	}
	private void log(String s)
	{
		System.out.println(s);
	}
	public static void main (String[] args)
	{
		Words w = new Words();
//		w.wordMania(20,84);
		w.uwm(48, 84);
	}
}
