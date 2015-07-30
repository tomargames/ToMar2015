import java.util.*;
import java.io.*;
import java.awt.*;
/**
 * This class can take a variable number of parameters on the command
 * line. Program execution begins with the main() method. The class
 * constructor is not invoked unless an object of type 'Class1'
 * created in the main() method.
 */
public class WordPlay
{
	public ArrayList readFile(String filename)
	{	
		ArrayList wds = new ArrayList();
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
	public void wordMania()
	{
		//initialize the word vectors
		ArrayList[] wl = new ArrayList[4];
		//read the word files in
		for (int i = 3; i < 7; i++)
		{	
			wl[i - 3] = readFile("words" + i + ".txt");
		}	
		try
		{
			PrintWriter log = new PrintWriter(new FileOutputStream("logout.txt"), true);
			PrintWriter outfile = new PrintWriter(new FileOutputStream("words6wm.txt"), true);
			//initialize ArrayLists
//			for (int r = 0; r < wl[3].size(); r++)  //for running through 6-letter words
//			{	
				String answerWord = "nation";
//				int answerCount[] = {0, 0, 0, 0}; //this is for analysis of word distribution
				int idx = 0;
				// set up answer vectors
				for (int a = 0; a < 4; a++)   // run through all 4 word lists
				{	
//					answerCount[a] = 0;
					for (int i = 0; i < wl[a].size(); i++)
					{
						String guess = (String) wl[a].get(i);
						if (goodWord(guess, answerWord) == true)
						{
							System.out.println(guess);
//							answerCount[a]++;
							idx++;
							if (idx > 40)
								break;	
						}	
					}
					if (idx > 40)
						break;
				}
				if (idx > 14 && idx < 41)
				{	
//					log.write(answerWord + " Total: " + idx
//							   + " Threes: " + answerCount[0]
//							   + " Fours: " + answerCount[1]
//							   + " Fives: " + answerCount[2]
//							   + " Sixes " + answerCount[3] + "\r\n");
					outfile.write(answerWord + "\r\n");
				}
				else
				{
					log("Dropping " + answerWord + idx);
				}	
//			}	
			log.close();
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
	private void findWords()
	{
		// find a word that has a word A?B and also B?A
		ArrayList a = readFile("wordsRest.txt");
//		ArrayList b = readFile("words4.txt");
		for (int i = 0; i < a.size(); i++)
		{
			String word1 = (String) a.get(i);
			if (word1.substring(1,2).equals(word1.substring(2,3)))
			{
				log(word1);	
			}
/*			for (int j = i+1; j < a.size(); j++)
			{
				String word2 = (String) a.get(j);
				if (word1.substring(2,3).equals(word2.substring(0,1)) &&
					word1.substring(0,1).equals(word2.substring(2,3)))
				{
					for (int k = 0; k < b.size(); k++)
					{
						String word3 = (String) b.get(k);
						String partial = word3.substring(0,3);
						if (partial.equals(word1) || partial.equals(word2))
						{
							log(word1 + " " + word2 + " " + word3);
						}
					}
				}
			}
*/		}
	}				
	private void use080127()
	{
		ArrayList a = readFile("words6.txt");
		for (int i = 0; i < a.size(); i++)
		{
			String word1 = (String) a.get(i);
			if (word1.substring(0,2).equalsIgnoreCase("ma"))
			{
				if (word1.substring(4,6).equalsIgnoreCase("ue"))
				{
					log(word1);
				}
			}
		}
	}
	private void process081115(ArrayList[] lists, String filename)
	{	
        try
        {
			BufferedReader br = new BufferedReader(new FileReader(filename));
            while   (true)
            {
                String s = br.readLine();
                if  (s == null)
                {
                    break;
                }
                s = s.toUpperCase().trim();
                if (s.length() < 10)
                {
                	// convert word to numbers
                	StringBuffer outString = new StringBuffer("");
                	for (int i = 0; i < s.length(); i++)
                	{
                		String l = s.substring(i, i+1);
                		if ("ABC".indexOf(l) > -1)
                		{
                			outString.append("2");
                		}
                		else if ("DEF".indexOf(l) > -1)
                		{
                			outString.append("3");
                		}
                		else if ("GHI".indexOf(l) > -1)
                		{
                			outString.append("4");
                		}
                		else if ("JKL".indexOf(l) > -1)
                		{
                			outString.append("5");
                		}
                		else if ("MNO".indexOf(l) > -1)
                		{
                			outString.append("6");
                		}
                		else if ("PQRS".indexOf(l) > -1)
                		{
                			outString.append("7");
                		}
                		else if ("TUV".indexOf(l) > -1)
                		{
                			outString.append("8");
                		}
                		else if ("WXYZ".indexOf(l) > -1)
                		{
                			outString.append("9");
                		}
                	}
                	// search to see if that number is there already
                	boolean found = false;
                	for (int i = 0; i < lists[s.length() - 3].size(); i++)
                	{
                		ArrayList a = (ArrayList) lists[s.length() - 3].get(i);
                		if (outString.toString().equalsIgnoreCase((String) a.get(0)))
                		{
                        	// if yes, add this word to it
                			a.add(s);
                			found = true;
                			break;
                		}
                	}
                	if (!found)
                	// if not, create a new ArrayList for it
                	{
                		ArrayList a = new ArrayList();
                		a.add(outString.toString());
                		a.add(s);
                		lists[s.length() - 3].add(a);
                	}
                }
            }
            br.close();
		}	
		catch   (Exception e)
		{
            log("Exception = " + e);
		}
	}
	private void use081115()
	{
		ArrayList lists[] = new ArrayList[7];
		for (int i = 0; i < 7; i++)
		{
			lists[i] = new ArrayList();
		}
		process081115(lists, "words3.txt");
		process081115(lists, "words4.txt");
		process081115(lists, "words5.txt");
		process081115(lists, "words6.txt");
		process081115(lists, "wordsRest.txt");
		for (int i = 0; i < 7; i++)
		{
			System.out.println("Words with length of " + (i + 3) + ": ");
			for (int j = 0; j < lists[i].size(); j++)
			{
				ArrayList a = (ArrayList) lists[i].get(j);
				if (a.size() > 3)
				{	
					StringBuffer sb = new StringBuffer((String) a.get(0) + ": ");
					for (int k = 1; k < a.size(); k++)
					{
						sb.append(a.get(k) + ", ");
					}
					System.out.println(sb.toString());
				}	
			}
		}
	}
	private void use080927()
	{ //ABC and ABCBD
		ArrayList a = readFile("words3.txt");
		ArrayList b = readFile("words5.txt");
		for (int i = 0; i < a.size(); i++)
		{
			String w1 = (String) a.get(i);
			String w1l2 = w1.substring(1,2);
			for (int j = 0; j < b.size(); j++)
			{
				String w2 = (String) b.get(j);
				String w2l123 = w2.substring(0,3);
				String w2l2 = w2.substring(1,2);
				String w2l4 = w2.substring(3,4);
				if (w1.equalsIgnoreCase(w2l123))
				{
//					log("Step1: " + w1 + " " + w2);
					if (w1l2.equalsIgnoreCase(w2l2))
					{
//						log("Step2: " + w1 + " " + w2);
						if (w1l2.equalsIgnoreCase(w2l4))
						{
							log("YES!: " + w1 + " " + w2);
						}
					}	
				}	
			}	
		}
	}
	private void use081120()
	{ //ABC and ABCBD
		ArrayList w4 = readFile("words4.txt");
		ArrayList w5 = readFile("words5.txt");
		for (int i = 0; i < w5.size(); i++)
		{
			String w1 = (String) w5.get(i);
			char[] ch1 = w1.toCharArray();
			if (ch1[0] == ch1[4])
			{
				for (int j = i + 1; j < w5.size(); j++)
				{	
					String w2 = (String) w5.get(j);
					char[] ch2 = w2.toCharArray();
					if (ch1[0] == ch2[0] & ch1[1] == ch2[1] & ch1[4] == ch2[4])
					{
						for (int k = j + 1; k < w5.size(); k++)
						{
							String w3 = (String) w5.get(k);
							char[] ch3 = w3.toCharArray();
							if (ch3[1] == ch2[1] & ch3[2] == ch2[2] & ch3[3] == ch2[3] & ch3[4] == ch2[4])
							{
								for (int l = 0; l < w4.size(); l++)
								{
									String b1 = (String) w4.get(l);
									char[] chb1 = b1.toCharArray();
									if (chb1[0] == ch1[0] & chb1[1] == ch1[1] & chb1[2] == ch1[2] & chb1[3] == ch2[2])
									{
										for (int m = l + 1; m < w4.size(); m++)
										{
											String b2 = (String) w4.get(m);
											char[] chb2 = b2.toCharArray();
											if (chb2[0] == chb1[0] & chb2[2] == chb1[3])
											{
												for (int n = m + 1; n < w4.size(); n++)
												{
													String b3 = (String) w4.get(n);
													char[] chb3 = b3.toCharArray();
													if (chb3[0] == chb2[0] & chb3[1] == chb2[1] & chb3[2] == chb2[2])
													{
														System.out.println("Step 4: " + w1 + " " + w2 + " " + w3 + " " + b1 + " " + b2 + " " + b3);
													}
												}	
											}
										}	
									}
								}	
							}
						}
					}
				}
			}
		}	
	}
	private void use090110()
	{
		ArrayList w = readFile("wordsRest.txt");
		ArrayList w3 = readFile("words3.txt");
		for (int i = 0; i < w.size(); i++)
		{
			String w1 = (String) w.get(i);
			if (w1.length() == 7)
			{	
				char[] ch1 = w1.toCharArray();
				if (ch1[2] == ch1[5] & ch1[3] == ch1[6])
				{
					for (int j = 0; j < w3.size(); j++)
					{
						String w2 = (String) w3.get(j);
						char[] ch3 = w2.toCharArray();
						if (ch1[4] == ch3[1] & ch1[5] == ch3[2])
						{
							for (int k = 0; k < w.size(); k++)
							{
								String w14 = (String) w.get(k);
								String checkString = w1 + w2 + ch1[6];
								if (w14.length() > 10 & w14.indexOf(checkString) > -1)
								{
									System.out.println(w14 + " " + w1 + " " + w2);
								}
							}
						}
					}
				}
			}	
		}
	}
	private void use091201()
	{
		ArrayList w = readFile("wordsRest.txt");
		for (int i = 0; i < w.size(); i++)
		{
			String w1 = (String) w.get(i);
			if (w1.length() == 10)
			{	
				char[] ch1 = w1.toCharArray();
				if (ch1[0] == ch1[2] & ch1[2] == ch1[8])
				{
					log(w1);
				}
			}	
		}
	}
	private boolean allLetterDifferent(char[] word)
	{
		for (int i = 0; i < word.length; i++)
		{
			for (int j = i + 1; j < word.length; j++)
			{
				if (word[i] == word[j])
				{
					return false;
				}
			}
		}
		return true;
	}
	private void use110810()
	{ 	//UXVHQZ
		// XVHQZ
		// EWVZQ
		//  XQUZ
		ArrayList a4 = readFile("words4.txt");
		ArrayList a5 = readFile("words5.txt");
		ArrayList a6 = readFile("words6.txt");
		for (int i = 0; i < a6.size(); i++)
		{
			String w6 = (String) a6.get(i);
			char[] ch6 = w6.toCharArray();
			if (allLetterDifferent(ch6))
			{	
				for (int j = 0; j < a4.size(); j++)
				{	
					String w4 = (String) a4.get(j);
					char[] ch4 = w4.toCharArray();
					if (allLetterDifferent(ch4)
					&& ch4[0] == ch6[1]
					&& ch4[1] == ch6[4]
					&& ch4[2] == ch6[0]
					&& ch4[3] == ch6[5])
					{
						for (int k = 0; k < a5.size(); k++)
						{	
							String w5a = (String) a5.get(k);
							char[] ch5a = w5a.toCharArray();
							if (allLetterDifferent(ch5a)
							&& ch5a[0] == ch6[1]		
							&& ch5a[1] == ch6[2]		
							&& ch5a[2] == ch6[3]		
							&& ch5a[3] == ch6[4]		
							&& ch5a[4] == ch6[5])
							{	
								for (int l = 0; l < a5.size(); l++)
								{	
									String w5b = (String) a5.get(l);
									char[] ch5b = w5b.toCharArray();
									if (allLetterDifferent(ch5b)
									&& ch5b[2] == ch6[2]		
									&& ch5b[3] == ch6[5]		
									&& ch5b[4] == ch6[4])
									{
										log (w6 + " " + w4 + " " + w5a + " " + w5b);
									}
								}
							}	
						}
					}
				}	
			}
		}
	}
			
	private void use110714()
	{
		ArrayList w = readFile("words4.txt");
		for (int i = 0; i < w.size(); i++)
		{
			String w1 = (String) w.get(i);   //ZAUV
			char[] ch1 = w1.toCharArray();
			for (int j = 0; j < w.size(); j++)
			{
				if (i != j)
				{	
					String w2 = (String) w.get(j);   //ZKVA
					char[] ch2 = w2.toCharArray();
					if (ch1[0] == ch2[0] && ch1[1] == ch2[3] && ch1[3] == ch2[2])
					{	
						for (int k = 0; k < w.size(); k++)
						{
							if (k != j)
							{	
								String w3 = (String) w.get(k);   //VAKN
								char[] ch3 = w3.toCharArray();
								if (ch2[1] == ch3[2] && ch2[2] == ch3[0] && ch2[3] == ch3[1])
								{	
									for (int l = 0; l < w.size(); l++)
									{
										if (k != l)
										{	
											String w4 = (String) w.get(l);   //ZUIV
											char[] ch4 = w4.toCharArray();
											if (ch1[0] == ch4[0] && ch1[2] == ch4[1] && ch1[3] == ch4[3])
											{
												log(w1 + " " + w2 + " " + w3 + " " + w4);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	
	private void jumble(String inWord)
	{
		ArrayList w = readFile("words" + inWord.length() + ".txt");
		String[] chars = new String[inWord.length()];
		boolean[] used = new boolean[inWord.length()];
		for (int i = 0; i < inWord.length(); i++)
		{
			chars[i] = inWord.substring(i, i+1);
		}
		for (int i = 0; i < w.size(); i++)
		{
			for (int j = 0; j < inWord.length(); j++)
			{
				used[j] = false;
			}
			String w1 = (String) w.get(i);
			int hits = 0;
			for (int j = 0; j < w1.length(); j++)
			{
				for (int k = 0; k < w1.length(); k++)
				{
					
					try
					{
						if (w1.substring(j, j+1).equalsIgnoreCase(chars[k]) & !used[k])
						{
							log(inWord + " : " + w1);
							used[k] = true;
							hits += 1;
							break;
						}
					} catch (Exception e)
					{
						// TODO Auto-generated catch block
						log("i = " + i + ", j = " + j + ", k = " + k);
						e.printStackTrace();
					}
				}
			}
			if (w1.length() == hits)
			{
				log(w1);
			}	
		}
		
	}
private void use130320()
{
    ArrayList w6 = readFile("words6.txt");
    ArrayList w3 = readFile("words3.txt");
    for (int i = 0; i < w6.size(); i++)
    {
        String wd6 = (String) w6.get(i);
        char[] ch6 = wd6.toCharArray();
        if (ch6[0] != ch6[3]
            || ch6[0] == ch6[1]
            || ch6[0] == ch6[2]
            || ch6[0] == ch6[4]
            || ch6[0] == ch6[5])
        {
            continue;
        }
        else
        {
            for (int j = 0; j < w3.size(); j++)
            {        
                String wd3 = (String) w3.get(j);
                char[] ch3 = wd3.toCharArray();
                if (ch3[0] != ch6[0]
                    || ch3[2] != ch6[5]
                    || ch3[2] == ch6[2]
                    || ch3[2] == ch6[3]
                    || ch3[2] == ch6[4])
                {
                    continue;
                }
                else
                {
                    log(wd6 + " " + wd3);
                }
            }        
        }    
    }
}               
	private void use120318()
	{
		ArrayList w = readFile("wordsRest.txt");
		for (int i = 0; i < w.size(); i++)
		{
			String w1 = (String) w.get(i);
			if (w1.length() == 7)
			{	
				char[] ch1 = w1.toCharArray();
				if (ch1[0] == ch1[6]
				&& ch1[0] != ch1[1]		
				&& ch1[0] != ch1[2]		
				&& ch1[0] != ch1[3]		
				&& ch1[0] != ch1[4]		
				&& ch1[0] != ch1[5]		
				&& ch1[1] != ch1[2]		
				&& ch1[1] != ch1[3]		
				&& ch1[1] != ch1[4]		
				&& ch1[1] != ch1[5]		
				&& ch1[2] != ch1[3]		
				&& ch1[2] != ch1[4]		
				&& ch1[2] != ch1[5])	
				{
					for (int j = 0; j < w.size(); j++)
					{	
						String w2 = (String) w.get(j);
						if (w2.length() == 8)
						{	
							char[] ch2 = w2.toCharArray();
							if (ch1[2] == ch2[0]
							&& ch1[2] == ch2[4]		
							&& ch1[5] == ch2[5]		
							&& ch1[4] == ch2[3]
							&& ch2[0] != ch2[1]
							&& ch2[1] != ch2[2]
							&& ch2[2] != ch2[3]
							&& ch2[3] != ch2[4]
							&& ch2[4] != ch2[5]
							&& ch2[5] != ch2[6]
							&& ch2[6] != ch2[7])
							{
								log(w1 + " " + w2);
							}
						}
					}	
				}
			}	
		}
	}
	public static void main (String[] args)
	{
		WordPlay w = new WordPlay();
		w.use130320();
	}
}
