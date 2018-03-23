public class Workerthread2 implements Runnable {
            private int randno;

            public void run() {
                Looper.prepare();//Looper

                wHandler2 = new Handler() {       //Handle the incoming messages
                    public void handleMessage(Message msg) {
                        int what = msg.what;
                        switch (what) {
                            case 1:
                                if (msg.arg1 == randno)
                                    Log.i("similarity", "same");
                                else {
                                    Message msg1 = mHandler.obtainMessage(6);//New message to send to the UI thread
                                    msg1.arg1 = 0;
                                    mHandler.sendMessage(msg1); //In this case the thread 2 provides an incorrect guess
                                }
                                break;
                            case 2:                             //Handle the GenerateGuess function call of thread1
                                break;
                        }
                    }
                };

                if(count%2!=0)
                playgame();

                Looper.loop();
            }

            void playgame() {
                Log.i("count2", String.valueOf(count));
                if (count < 20) {
                    GenerateRandom();                       //Generate a random number in thread 1
                    Workerthread1 w1 = new Workerthread1();
                    w1.GenerateGuess();
                    count++;
                    try {
                        Thread.sleep(1000);
                    } catch (InterruptedException e) {
                        System.out.println("Thread interrupted!");
                    };
                }
            }

            void GenerateRandom() {
                SecureRandom random = new SecureRandom();//Generating a random number in thread 1
                int num = random.nextInt(10000);
                String formatted = String.format("%05d", num);
                randno = Integer.valueOf(formatted);
                Message msg = mHandler.obtainMessage(2);
                msg.arg1 = randno;
                Log.i("random2",String.valueOf(randno));
                mHandler.sendMessage(msg);
            }

            private int guess1;

            void GenerateGuess() {
                SecureRandom random = new SecureRandom();//Thread 2 generates a random number
                int num = random.nextInt(10000);
                String formatted = String.format("%05d", num);
                guess1 = Integer.valueOf(formatted);
                /*Message msg1 = mHandler.obtainMessage(4);
                msg1.arg1 = guess1;*/
                Log.i("guess2",String.valueOf(guess1));
                //mHandler.sendMessage(msg1);

                Handler mainHandler = new Handler(Looper.getMainLooper());

                Runnable myRunnable = new Runnable() {
                    @Override
                    public void run() {
                        a1.add(guess1);
                        ArrayAdapter<Integer> adapter1 = new ArrayAdapter<Integer>(c, android.R.layout.simple_list_item_1, a1);
                        listview1.setAdapter(adapter1);
                    } // This is your code
                };
                mainHandler.post(myRunnable);


                wHandler1.post(new Runnable() {    //send guess generated by thread 2 to thread 1
                    public void run() {
                        Message msg = wHandler1.obtainMessage(1);
                        msg.arg1 = guess1;
                        wHandler1.sendMessage(msg);
                    }
                });
            }
        }
    }
