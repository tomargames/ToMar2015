import java.io.BufferedReader;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.PrintWriter;
import java.util.ArrayList;

public class AddWords 
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
	            log(filename + " in: " + wds.size() + " words");
	            br.close();
			}	
			catch   (Exception e)
			{
	            log("Exception = " + e);
			}
			return wds;
		}
		
		public ArrayList<String> writeFile(String filename, ArrayList<String> wds)
		{	
	        try
	        {
				PrintWriter outfile = new PrintWriter(new FileOutputStream(filename), true);
	            for (int i = 0; i < wds.size(); i++)
	            {
					outfile.write((String) wds.get(i) + "\r\n");
	            }
	            log(filename + " out: " + wds.size() + " words");
				outfile.close();
			}	
			catch   (Exception e)
			{
	            log("Exception = " + e);
			}
			return wds;
		}
		private void add()
		{
			//initialize the word vectors
			ArrayList<String>[] wl = new ArrayList[5];
			//read the word files in
			for (int i = 3; i < 7; i++)
			{	
				wl[i - 3] = readFile("words" + i + ".txt");
			}
			wl[4] = readFile("wordsRest.txt");
			ArrayList<String> wordIn = readFile("wordsToAdd.txt");
			for (int i = 0; i < wordIn.size(); i++)
			{
				String word = ((String) wordIn.get(i)).toLowerCase().trim();
				int wordIndex = word.length() - 3;
				wordIndex = (wordIndex > 4) ? 4 : wordIndex;
				wl[wordIndex].add(word);
			}
			//write the word files out
			for (int i = 3; i < 7; i++)
			{	
				 writeFile("words" + i + ".txt", wl[i - 3]);
			}
			writeFile("wordsRest.txt", wl[4]);
		}
		private void log(String s)
		{
			System.out.println(s);
		}

		public static void main (String[] args)
		{
			AddWords w = new AddWords();
			w.add();
		}
	}
